<?php

namespace MagicSystemsIO\Notifyre\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
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
            throw new RuntimeException('Failed to create SMS message');
        }

        return $message;
    }

    /**
     * @param Recipient[] $recipients
     */
    private static function createRecipients(array $recipients): Collection
    {
        $batchId = Str::uuid()->toString();

        $recipientData = array_map(function (Recipient $recipient, int $index) use ($batchId) {
            return [
                'id' => Str::uuid()->toString(),
                'tmp_id' => $batchId . '-' . $index,
                'type' => $recipient->type,
                'value' => $recipient->value,
            ];
        }, $recipients, array_keys($recipients));

        $affectedRows = NotifyreRecipients::upsert(
            values: $recipientData,
            uniqueBy: ['type', 'value'],
            update: ['tmp_id']
        );

        $expectedRows = count($recipientData);

        if ($affectedRows !== $expectedRows) {
            throw new RuntimeException(
                'Failed to process all recipients. Expected ' . $expectedRows .
                ', but got ' . $affectedRows
            );
        }

        return NotifyreRecipients::where('tmp_id', 'LIKE', $batchId . '-%')->get();
    }

    private static function linkMessageToRecipients(
        NotifyreSmsMessages $message,
        Collection $recipients
    ): void {
        $messageRecipients = $recipients->map(function (NotifyreRecipients $recipient) use ($message) {
            return [
                'sms_message_id' => $message->id,
                'recipient_id' => $recipient->id,
                'sent' => false,
            ];
        })->toArray();

        $affectedRows = NotifyreSmsMessageRecipient::upsert(
            values: $messageRecipients,
            uniqueBy: ['sms_message_id', 'recipient_id'],
            update: ['sent']
        );

        $expectedRows = count($messageRecipients);

        if ($affectedRows !== $expectedRows) {
            throw new RuntimeException(
                'Failed to create all message-recipient relationships. Expected ' .
                $expectedRows . ', but affected ' . $affectedRows . ' rows'
            );
        }
    }
}
