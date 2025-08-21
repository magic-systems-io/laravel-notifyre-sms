<?php

namespace Arbi\Notifyre\Http\Services;

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\Models\JunctionTables\NotifyreSMSMessageRecipient;
use Arbi\Notifyre\Models\NotifyreRecipients;
use Arbi\Notifyre\Models\NotifyreSMSMessages;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class NotifyreSMSMessageService
{
    public function getAllMessages(?string $sender): array
    {
        if (empty($sender) || (is_string($sender) && empty(trim($sender)))) {
            return [];
        }

        return NotifyreSMSMessages::where('sender', $sender)->get()->toArray();
    }

    /**
     * Create a new SMS message with recipients
     *
     * @param RequestBodyDTO $data
     *
     * @throws Throwable
     * @throws RuntimeException
     * @return array
     */
    public function createMessage(RequestBodyDTO $data): array
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
    private function createSMSMessage(RequestBodyDTO $data): NotifyreSMSMessages
    {
        $message = NotifyreSMSMessages::create([
            'sender' => $data->sender,
            'body' => $data->body,
        ]);

        if (!$message->id) {
            throw new RuntimeException('Failed to create SMS message');
        }

        return $message;
    }

    /**
     * Create recipients and return collection with IDs
     *
     * @param Recipient[] $recipients
     *
     * @return Collection
     */
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
     * Link message to recipients via junction table
     */
    private function linkMessageToRecipients(NotifyreSMSMessages $message, Collection $recipients): void
    {
        $messageRecipientData = $recipients->map(function (NotifyreRecipients $recipient) use ($message) {
            return [
                'notifyre_sms_message_id' => $message->id,
                'notifyre_recipient_id' => $recipient->id,
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
