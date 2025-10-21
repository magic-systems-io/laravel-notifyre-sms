<?php

namespace MagicSystemsIO\Notifyre\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBody;
use MagicSystemsIO\Notifyre\Models\JunctionTables\NotifyreSmsMessageRecipient;
use MagicSystemsIO\Notifyre\Models\NotifyreRecipients;
use MagicSystemsIO\Notifyre\Models\NotifyreSmsMessages;
use RuntimeException;
use Throwable;

class NotifyreMessagePersister
{
    /**
     * @throws Throwable
     */
    public static function persist(RequestBody $request, ResponseBody $response): void
    {
        DB::transaction(function () use ($request, $response) {
            $message = self::createSmsMessage($request, $response->payload->id);
            $recipients = self::createRecipients($request->recipients);
            self::linkMessageToRecipients($message, $recipients);
        });
    }

    private static function createSmsMessage(RequestBody $request, string $messageId): NotifyreSmsMessages
    {
        $message = NotifyreSmsMessages::create([
            'id' => $messageId,
            'sender' => $request->sender,
            'body' => $request->body,
            'driver' => config('services.notifyre.driver') ?? config('notifyre.driver'),
        ]);

        if (!$message->wasRecentlyCreated) {
            Log::channel('notifyre')->error('Failed to create SMS message', ['message' => $message]);

            throw new RuntimeException('Failed to create SMS message');
        }

        Log::channel('notifyre')->info('Created SMS message', ['message' => $message]);

        return $message;
    }

    /**
     * @param Recipient[] $recipients
     */
    private static function createRecipients(array $recipients): Collection
    {
        $recipientData = array_map(function (Recipient $recipient) {
            return [
                'id' => Str::uuid()->toString(),
                'type' => $recipient->type,
                'value' => $recipient->value,
            ];
        }, $recipients);

        NotifyreRecipients::upsert(
            values: $recipientData,
            uniqueBy: ['type', 'value'],
            update: []
        );

        $groupedByType = [];
        foreach ($recipientData as $data) {
            $groupedByType[$data['type']][] = $data['value'];
        }

        $query = NotifyreRecipients::query();
        foreach ($groupedByType as $type => $values) {
            $query->orWhere(function ($q) use ($type, $values) {
                $q->where('type', $type)->whereIn('value', $values);
            });
        }

        $foundRecipients = $query->get()->keyBy(function ($recipient) {
            return $recipient->type . '|' . $recipient->value;
        });

        $orderedRecipients = [];
        foreach ($recipientData as $data) {
            $key = $data['type'] . '|' . $data['value'];
            $orderedRecipients[] = $foundRecipients[$key];
        }

        Log::channel('notifyre')->info('Processed all recipients', [
            'count' => count($orderedRecipients),
        ]);

        return new Collection($orderedRecipients);
    }

    private static function linkMessageToRecipients(
        NotifyreSmsMessages $message,
        Collection $recipients
    ): void {
        $messageRecipients = $recipients->map(function (NotifyreRecipients $recipient) use ($message) {
            return [
                'sms_message_id' => $message->id,
                'recipient_id' => $recipient->id,
                'delivery_status' => 'pending',
            ];
        })->toArray();

        $affectedRows = NotifyreSmsMessageRecipient::upsert(
            values: $messageRecipients,
            uniqueBy: ['sms_message_id', 'recipient_id'],
            update: ['delivery_status']
        );

        $expectedRows = count($messageRecipients);

        if ($affectedRows !== $expectedRows) {
            Log::channel('notifyre')->error("Failed to create all message-recipient relationships. Expected $expectedRows but affected $affectedRows rows");

            throw new RuntimeException(
                'Failed to create all message-recipient relationships. Expected ' .
                $expectedRows . ', but affected ' . $affectedRows . ' rows'
            );
        }

        Log::channel('notifyre')->info("Created all message-recipient relationships. Expected $expectedRows and affected $affectedRows rows");
    }
}
