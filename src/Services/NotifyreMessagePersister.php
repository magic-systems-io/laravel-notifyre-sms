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
            self::linkMessageToRecipients(
                message:    $message,
                recipients: $recipients,
                response:   $response
            );
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

        if (!$message->id) {
            throw new RuntimeException('Failed to create SMS message');
        }

        return $message;
    }

    /**
     * @var Recipient[] $recipients
     */
    private static function createRecipients(array $recipients): Collection
    {
        $recipientData = array_map(function ($recipient) {
            $id = Str::uuid()->toString();

            return [
                'id' => $id,
                'tmp_id' => $id,
                'type' => $recipient->type,
                'value' => $recipient->value,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $recipients);

        NotifyreRecipients::upsert(
            values:   $recipientData,
            uniqueBy: ['type', 'value'],
            update:   ['tmp_id', 'updated_at']
        );

        return NotifyreRecipients::whereIn('tmp_id', array_column($recipientData, 'tmp_id'))->get();
    }

    private static function linkMessageToRecipients(NotifyreSmsMessages $message, Collection $recipients, ResponseBody $response): void
    {
        $messageRecipients = $recipients->map(function (NotifyreRecipients $recipient) use ($message, $response) {
            return [
                'sms_message_id' => $message->id,
                'recipient_id' => $recipient->id,
                'sent' => false,
            ];
        })->toArray();

        $junctionCreated = NotifyreSmsMessageRecipient::upsert(
            values:   $messageRecipients,
            uniqueBy: ['sms_message_id', 'recipient_id'],
            update:   ['sent']
        );

        if (!$junctionCreated) {
            throw new RuntimeException('Failed to create message-recipient relationships');
        }
    }
}
