<?php

namespace MagicSystemsIO\Notifyre\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\Enums\NotifyreRecipientTypes;
use MagicSystemsIO\Notifyre\Http\Requests\NotifyreSMSMessagesRequest;
use MagicSystemsIO\Notifyre\Models\NotifyreSMSMessages;
use MagicSystemsIO\Notifyre\Services\NotifyreService;
use Throwable;

class NotifyreSMSController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $sender = $request->user()?->getSender();
        if (empty($sender) || (empty(trim($sender)))) {
            return response()->json(['error' => 'Sender parameter is required'], 422);
        }

        $messages = NotifyreSMSMessages::where('sender', $sender)->get()->toArray();

        return response()->json($this->paginate($request, $messages));
    }

    public function store(NotifyreSMSMessagesRequest $request): JsonResponse
    {
        try {
            $message = NotifyreService::send($this->buildMessageData($request));

            return response()->json($message);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function buildMessageData(NotifyreSMSMessagesRequest $request): RequestBody
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

    public function show(int $sms): JsonResponse
    {
        if (config('notifyre.api.cache.enabled')) {
            $key = config('notifyre.api.cache.prefix');
            $message = Cache::get("$key.$sms");
        } else {
            $message = NotifyreSMSMessages::with('messageRecipients.recipient')->find($sms);
        }

        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }

        return response()->json($message);
    }
}
