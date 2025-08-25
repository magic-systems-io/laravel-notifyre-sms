<?php

namespace MagicSystemsIO\Notifyre\Http\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\Models\JunctionTables\NotifyreSMSMessageRecipient;
use MagicSystemsIO\Notifyre\Models\NotifyreRecipients;
use MagicSystemsIO\Notifyre\Models\NotifyreSMSMessages;
use RuntimeException;
use Throwable;

class NotifyreSMSMessageService
{
    public function getAllMessages(?string $sender): array
    {
        if (empty($sender) || (empty(trim($sender)))) {
            return [];
        }

        return NotifyreSMSMessages::where('sender', $sender)->get()->toArray();
    }

    /**
     * Create a new SMS message with recipients
     *
     * @param RequestBody $data
     *
     *@throws RuntimeException
     * @throws Throwable
     * @return array
     */
    public function createMessage(RequestBody $data): array
    {
        return DB::transaction(function () use ($data) {
            $message = $this->createSMSMessage($data);
            $recipients = $this->createRecipients($data->recipients);
            $this->linkMessageToRecipients($message, $recipients);

            return $this->formatMessageResponse($message, $recipients);
        });
    }

    /**
     * Create the main SMS message
     */
    private function createSMSMessage(RequestBody $data): NotifyreSMSMessages
    {
        $message = NotifyreSMSMessages::create([
            'messageId' => uniqid('msg_', true),
            'sender' => $data->sender,
            'body' => $data->body,
        ]);

        if (!$message->id) {
            throw new RuntimeException('Failed to create SMS message');
        }

        return $message;
    }

    /**
 * Create or retrieve existing recipients to avoid duplicates
 */
    private function createRecipients(array $recipients): Collection
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
            ->whereIn('value', array_column($recipientData, 'value'))
            ->whereIn('type', array_column($recipientData, 'type'))
            ->get();
    }

    /**
     * Create recipients and return collection with IDs
     *
     * @param Recipient[] $recipients
     *
     * @return Collection
     */

    /**
     * Link message to recipients via junction table
     */
    private function linkMessageToRecipients(NotifyreSMSMessages $message, Collection $recipients): void
    {
        $messageRecipientData = $recipients->map(function (NotifyreRecipients $recipient) use ($message) {
            return [
                'sms_message_id' => $message->id,
                'recipient_id' => $recipient->id,
            ];
        })->toArray();

        $junctionCreated = NotifyreSMSMessageRecipient::insert($messageRecipientData);

        if (!$junctionCreated) {
            throw new RuntimeException('Failed to create message-recipient relationships');
        }
    }

    /**
     * Format the response data
     */
    private function formatMessageResponse(NotifyreSMSMessages $message, Collection $recipients): array
    {
        return [
            'id' => $message->id,
            'sender' => $message->sender,
            'body' => $message->body,
            'created_at' => $message->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $message->updated_at->format('Y-m-d H:i:s'),
            'recipients' => $recipients->toArray(),
        ];
    }

    public function getMessageById(int $smsId): ?NotifyreSMSMessages
    {
        return NotifyreSMSMessages::with('messageRecipients.recipient')->find($smsId);
    }
}
