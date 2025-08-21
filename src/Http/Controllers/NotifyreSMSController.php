<?php

namespace Arbi\Notifyre\Http\Controllers;

use Arbi\Notifyre\Contracts\NotifyreServiceInterface;
use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\Http\Requests\NotifyreSMSMessagesRequest;
use Arbi\Notifyre\Http\Services\NotifyreSMSMessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Throwable;

class NotifyreSMSController extends Controller
{
    public function __construct(
        protected NotifyreSMSMessageService $notifyreSMSMessageService,
        protected NotifyreServiceInterface $notifyreService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $messages = $this->notifyreSMSMessageService->getAllMessages($request->user()?->getSender());

        return response()->json($this->paginate($request, $messages));
    }

    public function store(NotifyreSMSMessagesRequest $request): JsonResponse
    {
        try {
            $recipients = array_map(fn ($recipient) => new Recipient(
                type:  $recipient['type'],
                value: $recipient['value']
            ), $request->validated(
                'recipients'
            ));
            $messageData = new RequestBodyDTO(
                body:       $request->validated('body'),
                sender:     $request->validated('sender'),
                recipients: $recipients,
            );
            $message = $this->notifyreSMSMessageService->createMessage($messageData);

            $this->notifyreService->send($messageData);

            return response()->json($message);
        } catch (RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function show(int $sms): JsonResponse
    {
        $message = $this->notifyreSMSMessageService->getMessageById($sms);
        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }

        return response()->json($message);
    }
}
