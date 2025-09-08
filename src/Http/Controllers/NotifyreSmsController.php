<?php

namespace MagicSystemsIO\Notifyre\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\DTO\SMS\SmsRecipient;
use MagicSystemsIO\Notifyre\Enums\NotifyreRecipientTypes;
use MagicSystemsIO\Notifyre\Http\Requests\NotifyreSmsCallbackRequest;
use MagicSystemsIO\Notifyre\Http\Requests\NotifyreSmsMessagesRequest;
use MagicSystemsIO\Notifyre\Models\JunctionTables\NotifyreSmsMessageRecipient;
use MagicSystemsIO\Notifyre\Models\NotifyreRecipients;
use MagicSystemsIO\Notifyre\Models\NotifyreSmsMessages;
use MagicSystemsIO\Notifyre\Services\NotifyreService;
use RuntimeException;
use Throwable;

class NotifyreSmsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $sender = $request->user()?->getSender();
        if (empty($sender) || (empty(trim($sender)))) {
            return response()->json(['error' => 'Sender parameter is required'], 422);
        }

        $messages = NotifyreSmsMessages::where('sender', $sender)->get()->toArray();

        return response()->json($this->paginate($request, $messages));
    }

    public function store(NotifyreSmsMessagesRequest $request): JsonResponse
    {
        try {
            NotifyreService::send($this->buildMessageData($request));

            return response()->json('Message is being sent', 201);
        } catch (Throwable $e) {
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

    public function show(string $sms): JsonResponse
    {
        if (!$message = NotifyreSmsMessages::with('messageRecipients.recipient')->find($sms)) {
            return response()->json(['error' => 'Message not found'], 404);
        }

        return response()->json($message);
    }

    public function getApi(string $sms): JsonResponse
    {
        try {
            $response = NotifyreService::get($sms);

            return response()->json($response);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function listApi(Request $request): JsonResponse
    {
        try {
            $response = NotifyreService::list($request->query());

            return response()->json($response);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function callback(NotifyreSmsCallbackRequest $request): JsonResponse
    {
        try {
            $responseBody = $request->toResponseBody();

            $message = NotifyreSmsMessages::find($responseBody->payload->id);
            if (!$message) {
                throw new RuntimeException('Message not found');
            }

            DB::transaction(function () use ($message, $responseBody) {
                $this->updateRecipientIdentStatus($message->recipients()->get()->toArray(), $responseBody->payload->recipients);
                $this->updateRecipientSentStatus($message, $responseBody->payload->recipients);
            });

            return response()->json($message->fresh('recipients'));
        } catch (Throwable $e) {
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
            if ($payloadRecipient = $payloadMap->get($recipient->value)) {
                return [
                    'id' => $payloadRecipient->id,
                    'tmp_id' => $recipient->tmp_id,
                    'type' => $recipient->type,
                    'value' => $recipient->value,
                ];
            }

            return null;
        }, $recipients));

        if (!empty($upsertData)) {
            NotifyreRecipients::upsert(
                $upsertData,
                ['tmp_id'],
                ['id', 'tmp_id']
            );

            $tmpIds = collect($upsertData)->pluck('tmp_id')->toArray();
            NotifyreRecipients::whereIn('tmp_id', $tmpIds)->update(['tmp_id' => null]);
        }
    }

    private function updateRecipientSentStatus(NotifyreSmsMessages $message, array $callbackRecipients): void
    {
        $callbackStatusMap = [];
        foreach ($callbackRecipients as $callbackRecipient) {
            $callbackStatusMap[$callbackRecipient->toNumber] = $callbackRecipient->status;
        }

        $recipients = NotifyreRecipients::whereHas('notifyreSmsMessageRecipients', function ($query) use ($message) {
            $query->where('sms_message_id', $message->id);
        })->get();

        $upsertData = [];
        foreach ($recipients as $recipient) {
            $isSent = false;

            if (isset($callbackStatusMap[$recipient->value])) {
                $isSent = in_array($callbackStatusMap[$recipient->value], ['sent', 'delivered']);
            }

            $upsertData[] = [
                'sms_message_id' => $message->id,
                'recipient_id' => $recipient->id,
                'sent' => $isSent,
            ];
        }

        NotifyreSmsMessageRecipient::upsert(
            $upsertData,
            ['sms_message_id', 'recipient_id'],
            ['sent']
        );
    }
}
