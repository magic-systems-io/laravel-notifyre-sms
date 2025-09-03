<?php

namespace MagicSystemsIO\Notifyre\Http\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBody;
use MagicSystemsIO\Notifyre\Models\JunctionTables\NotifyreSMSMessageRecipient;
use MagicSystemsIO\Notifyre\Models\NotifyreRecipients;
use MagicSystemsIO\Notifyre\Models\NotifyreSMSMessages;
use Psr\SimpleCache\InvalidArgumentException;
use RuntimeException;
use Throwable;

class SMSMessagePersister
{
    /**
     * Persist SMS message with recipients to database
     *
     * @param RequestBody $request
     * @param ResponseBody $response
     *
     * @throws Throwable
     * @return array
     */
    public static function persist(RequestBody $request, ResponseBody $response): array
    {
        $formatedResponse =  DB::transaction(function () use ($request, $response) {
            $message = self::createSMSMessage($request, $response->payload->smsMessageID);
            $recipients = self::createRecipients($request->recipients);
            self::linkMessageToRecipients($message, $recipients, $response);

            return self::formatMessageResponse($message, $recipients, $response);
        });

        if (config('notifyre.api.cache.enabled')) {
            self::cache($response);
        }

        return $formatedResponse;
    }

    /**
     * @param ResponseBody $responseBodyDTO
     *
     * @throws InvalidArgumentException
     */
    private static function cache(ResponseBody $responseBodyDTO): void
    {
        if (!class_exists(Cache::class) || !Cache::getStore()) {
            return;
        }

        $key = config('notifyre.api.cache.prefix');
        $ttl  = config('notifyre.api.cache.ttl');
        Cache::set("$key.{$responseBodyDTO->payload->smsMessageID}", $responseBodyDTO, $ttl);
    }

    /**
     * Create the main SMS message
     */
    private static function createSMSMessage(RequestBody $request, string $messageId): NotifyreSMSMessages
    {
        $message = NotifyreSMSMessages::create([
            'messageId' => $messageId,
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
     * Create recipients and return collection with IDs
     */
    private static function createRecipients(array $recipients): Collection
    {
        $recipientData = collect($recipients)->map(function (Recipient $recipient) {
            return [
                'type' => $recipient->type,
                'value' => $recipient->value,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        NotifyreRecipients::upsert(
            values:    $recipientData,
            uniqueBy: ['type', 'value'],
            update:   ['updated_at']
        );

        return NotifyreRecipients::query()
            ->whereIn('value', collect($recipients)->pluck('value'))
            ->whereIn('type', collect($recipients)->pluck('type'))
            ->get();
    }

    /**
     * Link message to recipients via junction table
     */
    private static function linkMessageToRecipients(NotifyreSMSMessages $message, Collection $recipients, ResponseBody $response): void
    {
        $messageRecipients = $recipients->map(function (NotifyreRecipients $recipient) use ($message, $response) {
            return [
                'sms_message_id' => $message->id,
                'recipient_id' => $recipient->id,
                'sent' => !in_array($recipient->value, $response->payload->invalidToNumbers),
                'message' => $response->message,
            ];
        })->toArray();

        $junctionCreated = NotifyreSMSMessageRecipient::insert($messageRecipients);

        if (!$junctionCreated) {
            throw new RuntimeException('Failed to create message-recipient relationships');
        }
    }

    /**
     * Format the response data
     */
    private static function formatMessageResponse(NotifyreSMSMessages $message, Collection $recipients, ResponseBody $response): array
    {
        return [
            'id' => $message->id,
            'sender' => $message->sender,
            'body' => $message->body,
            'created_at' => $message->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $message->updated_at->format('Y-m-d H:i:s'),
            'recipients' => $recipients->map(function (NotifyreRecipients $recipient) use ($response) {
                return [
                    'id' => $recipient->id,
                    'type' => $recipient->type,
                    'value' => $recipient->value,
                    'sent' => !in_array($recipient->value, $response->payload->invalidToNumbers),
                    'message' => $response->message,
                ];
            })->toArray(),
        ];
    }
}
