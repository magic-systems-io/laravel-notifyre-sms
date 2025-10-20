<?php

namespace MagicSystemsIO\Notifyre\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MagicSystemsIO\Notifyre\Contracts\NotifyreManager;
use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\DTO\SMS\SmsRecipient;
use MagicSystemsIO\Notifyre\Enums\NotifyProcessedStatus;
use MagicSystemsIO\Notifyre\Enums\NotifyreRecipientTypes;
use MagicSystemsIO\Notifyre\Http\Requests\NotifyreSmsCallbackRequest;
use MagicSystemsIO\Notifyre\Http\Requests\NotifyreSmsMessagesRequest;
use MagicSystemsIO\Notifyre\Models\JunctionTables\NotifyreSmsMessageRecipient;
use MagicSystemsIO\Notifyre\Models\NotifyreRecipients;
use MagicSystemsIO\Notifyre\Models\NotifyreSmsMessages;
use Throwable;

class NotifyreSmsController extends Controller
{
    public function indexMessages(Request $request): JsonResponse
    {
        $sender = $request->user()?->getSender();
        if (empty($sender) || (empty(trim($sender)))) {
            Log::channel('notifyre')->error('Notifyre SMS index messages', ['error' => 'Sender parameter is required']);

            return response()->json(['error' => 'Sender parameter is required'], 422);
        }

        $messages = NotifyreSmsMessages::where('sender', $sender)->get()->toArray();

        Log::channel('notifyre')->info('Notifyre SMS index messages', ['messages' => $messages]);

        return response()->json($this->paginate($request, $messages));
    }

    public function sendMessage(NotifyreSmsMessagesRequest $request): JsonResponse
    {
        try {
            app(NotifyreManager::class)->send($this->buildMessageData($request));

            Log::channel('notifyre')->info('Notifyre SMS send message', ['request' => $request]);

            return response()->json('Message is being sent', 201);
        } catch (Throwable $e) {
            Log::channel('notifyre')->error('Notifyre SMS send message', ['error' => $e]);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function buildMessageData(NotifyreSmsMessagesRequest $request): RequestBody
    {
        $recipients = array_map(fn ($recipient) => new Recipient(
            type:  $recipient['type'] ?? NotifyreRecipientTypes::MOBILE_NUMBER->value,
            value: $recipient['value']
        ), $request->validated('recipients'));

        return new RequestBody(
            body:       $request->validated('body'),
            recipients: $recipients,
            sender:     $request->validated('sender'),
        );
    }

    public function showMessage(string $sms): JsonResponse
    {
        if (!$message = NotifyreSmsMessages::with('recipients')->find($sms)) {
            Log::channel('notifyre')->error('Message not found', ['sms' => $sms]);

            return response()->json(['error' => 'Message not found'], 404);
        }

        Log::channel('notifyre')->info('Notifyre SMS show', ['message' => $message]);

        return response()->json($message);
    }

    public function showMessagesSentToRecipient(string $recipient): JsonResponse
    {
        if (!$recipient = NotifyreRecipients::with('smsMessages')->find($recipient)) {
            Log::channel('notifyre')->error('Message not found', ['recipient' => $recipient]);

            return response()->json('Recipient not found', 404);
        }

        Log::channel('notifyre')->info('Notifyre SMS show messages sent to recipient', ['recipient' => $recipient]);

        return response()->json($recipient);
    }

    public function getFromNotifyre(string $sms): JsonResponse
    {
        try {
            $response = app(NotifyreManager::class)->get($sms);
            Log::channel('notifyre')->info('Notifyre SMS get', ['response' => $response]);

            return response()->json($response);
        } catch (Throwable $e) {
            Log::channel('notifyre')->error('Failed to get message from Notifyre', ['error' => $e]);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function indexFromNotifyre(Request $request): JsonResponse
    {
        try {
            $response = app(NotifyreManager::class)->list($request->query());
            Log::channel('notifyre')->info('Notifyre SMS list', ['response' => $response]);

            return response()->json($response);
        } catch (Throwable $e) {
            Log::channel('notifyre')->error('Failed to list messages from Notifyre', ['error' => $e]);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function handleCallback(NotifyreSmsCallbackRequest $request): JsonResponse
    {
        $messageId = $request->validated('Payload.ID');

        try {
            $message = $this->findMessageWithRetry($messageId);
            if (!$message) {
                return response()->json(['message' => 'Message not found'], 404);
            }

            $recipient = $request->getRecipient();
            $result = $this->handleRecipient($messageId, $recipient);

            if ($result !== true) {
                return $result;
            }

            Log::channel('notifyre')->info('Webhook processed successfully', [
                'message_id' => $messageId,
                'recipient' => $recipient->toNumber,
                'status' => $recipient->status,
                'delivery_status' => $recipient->deliveryStatus,
            ]);

            return response()->json(['success' => true, 'message' => 'Webhook processed']);
        } catch (Throwable $e) {
            Log::channel('notifyre')->error('Failed to process webhook', [
                'message_id' => $messageId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function findMessageWithRetry(string $messageId): ?NotifyreSmsMessages
    {
        $maxRetries = config('notifyre.webhook.retry_attempts', 3);
        $delaySeconds = config('notifyre.webhook.retry_delay', 1);

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {

            $message = NotifyreSmsMessages::find($messageId);
            if ($message) {
                return $message;
            }

            if ($attempt < $maxRetries) {
                Log::channel('notifyre')->debug("Message not found, retry attempt $attempt/$maxRetries");
                sleep($delaySeconds);
            }
        }

        Log::channel('notifyre')->warning('Message not found after retries', [
            'message_id' => $messageId,
            'attempts' => $maxRetries,
        ]);

        return null;
    }

    /**
     * @throws Throwable
     */
    private function handleRecipient(string $messageId, SmsRecipient $recipient): JsonResponse|bool
    {
        return DB::transaction(function () use ($messageId, $recipient) {

            $localRecipient = NotifyreRecipients::where('value', $recipient->toNumber)->first();

            if (!$localRecipient) {
                Log::channel('notifyre')->warning('Recipient not found', [
                    'message_id' => $messageId,
                    'recipient' => $recipient->toNumber,
                ]);

                return response()->json(['message' => 'Recipient not found'], 404);
            }

            $pivot = NotifyreSmsMessageRecipient::where('sms_message_id', $messageId)
                ->where('recipient_id', $localRecipient->id)
                ->first();

            if ($pivot && $pivot->sent) {
                Log::channel('notifyre')->debug('Webhook already processed (idempotent)', [
                    'message_id' => $messageId,
                    'recipient' => $recipient->toNumber,
                ]);

                return response()->json(['success' => true, 'message' => 'Webhook already processed']);
            }

            if ($localRecipient->id !== $recipient->id) {
                $localRecipient->id = $recipient->id;
                $localRecipient->save();
            }

            NotifyreSmsMessageRecipient::updateOrCreate(
                ['sms_message_id' => $messageId, 'recipient_id' => $recipient->id],
                ['sent' => NotifyProcessedStatus::isStatusSuccessful($recipient->deliveryStatus)]
            );

            return true;
        });
    }
}
