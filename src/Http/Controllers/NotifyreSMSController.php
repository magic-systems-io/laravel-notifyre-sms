<?php

namespace Arbi\Notifyre\Http\Controllers;

use Arbi\Notifyre\Contracts\NotifyreServiceInterface;
use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\DTO\SMS\ResponseBodyDTO;
use Arbi\Notifyre\Http\Requests\NotifyreSMSMessagesRequest;
use Arbi\Notifyre\Http\Services\NotifyreSMSMessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;
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
            $messageData = $this->buildMessageData($request);
            $response = $this->notifyreService->send($messageData);

            if (empty($response) || !$response->success) {
                return response()->json(['message' => 'Failed to send SMS'], 422);
            }

            if (!empty($failedRecipients = $response->payload->invalidToNumbers)) {
                $successfulRecipients = $this->filterSuccessfulRecipients($messageData->recipients, $failedRecipients);
                $messageData = new RequestBodyDTO(
                    body: $messageData->body,
                    sender: $messageData->sender,
                    recipients: $successfulRecipients
                );
            }

            if ($request->validated('persist') ?? config('notifyre.api.database.enabled')) {
                $message = $this->notifyreSMSMessageService->createMessage($messageData);
            }

            if (config('notifyre.api.cache.enabled')) {
                $this->cache($response);
            }

            return response()->json([
                'data' => $message ?? $messageData->toArray(),
                'failed_recipients' => $response->payload->invalidToNumbers ?? [],
            ]);
        } catch (RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function buildMessageData(NotifyreSMSMessagesRequest $request): RequestBodyDTO
    {
        $recipients = array_map(fn ($recipient) => new Recipient(
            type:  $recipient['type'],
            value: $recipient['value']
        ), $request->validated(
            'recipients'
        ));

        return new RequestBodyDTO(
            body:       $request->validated('body'),
            sender:     $request->validated('sender'),
            recipients: $recipients,
        );
    }

    /**
     * @param Recipient[] $intendedRecipients
     * @param array $failedRecipients
     *
     * @return array
     */
    private function filterSuccessfulRecipients(array $intendedRecipients, array $failedRecipients): array
    {
        return array_filter($intendedRecipients, function (Recipient $recipient) use ($failedRecipients) {
            return !in_array($recipient->value, $failedRecipients);
        });
    }

    /**
     * @throws InvalidArgumentException
     */
    private function cache(ResponseBodyDTO $responseBodyDTO): void
    {
        if (!class_exists(Cache::class) || !Cache::getStore()) {
            return;
        }

        $key = config('notifyre.api.cache.prefix');
        $ttl  = config('notifyre.api.cache.ttl');
        Cache::set("$key.{$responseBodyDTO->payload->smsMessageID}", $responseBodyDTO, $ttl);
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
