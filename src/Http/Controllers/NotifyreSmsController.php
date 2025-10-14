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

    public function handleWebhook(NotifyreSmsCallbackRequest $request): JsonResponse
    {
        Log::channel('notifyre')->info('Notifyre SMS handle', ['request' => $request]);

        try {
            $maxRetries = config('notifyre.webhook.retry_attempts');
            $delaySeconds = config('notifyre.webhook.retry_delay');

            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                $responseBody = $request->toResponseBody();
                $message = NotifyreSmsMessages::find($responseBody->payload->id);

                if (!$message) {
                    sleep($delaySeconds);

                    continue;
                }

                DB::transaction(function () use ($message, $responseBody) {
                    $this->updateRecipientIdentStatus($message->recipients()->get()->all(), $responseBody->payload->recipients);
                    $this->updateRecipientSentStatus($message, $responseBody->payload->recipients);
                });

                $message = $message->fresh('recipients');

                Log::channel('notifyre')->info('Message updated', ['message' => $message]);

                return response()->json($message);
            }

            Log::channel('notifyre')->error("Message not found after $maxRetries attempts");

            return response()->json(['message' => "Message not found after $maxRetries attempts"], 404);

        } catch (Throwable $e) {
            Log::channel('notifyre')->error('Failed to process Notifyre webhook', ['error' => $e]);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @param NotifyreRecipients[] $recipients
     * @param SmsRecipient[] $payloadRecipients
     */
    private function updateRecipientIdentStatus(array $recipients, array $payloadRecipients): void
    {
        $payloadMap = collect($payloadRecipients)->keyBy('toNumber');

        $upsertData = array_filter(array_map(function ($recipient) use ($payloadMap) {
            if (!$payloadRecipient = $payloadMap->get($recipient->value)) {
                return null;
            }

            return [
                'id' => $payloadRecipient->id,
                'tmp_id' => $recipient->tmp_id,
                'type' => $recipient->type,
                'value' => $recipient->value,
            ];
        }, $recipients));

        if (!empty($upsertData)) {
            NotifyreRecipients::upsert(
                $upsertData,
                ['tmp_id'],
                ['id', 'tmp_id']
            );

            NotifyreRecipients::query()
                ->whereIn('tmp_id', collect($upsertData)->pluck('tmp_id')->toArray())
                ->update(['tmp_id' => null]);
        }
    }

    private function updateRecipientSentStatus(NotifyreSmsMessages $message, array $callbackRecipients): void
    {
        $callbackStatusMap = array_column($callbackRecipients, 'status', 'toNumber');

        $recipients = NotifyreRecipients::whereIn('value', array_keys($callbackStatusMap))->get()->all();

        $upsertData = array_map(function ($recipient) use ($message, $callbackStatusMap) {
            return [
                'sms_message_id' => $message->id,
                'recipient_id' => $recipient->id,
                'sent' => isset($callbackStatusMap[$recipient->value]) && in_array($callbackStatusMap[$recipient->value], ['sent', 'delivered']),
            ];
        }, $recipients);

        NotifyreSmsMessageRecipient::upsert(
            $upsertData,
            ['sms_message_id', 'recipient_id'],
            ['sent']
        );
    }
}
