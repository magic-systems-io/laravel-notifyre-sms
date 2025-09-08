<?php

namespace MagicSystemsIO\Notifyre\DTO\SMS;

use Illuminate\Contracts\Support\Arrayable;

readonly class ResponseBody implements Arrayable
{
    public function __construct(
        public bool $success,
        public int $statusCode,
        public string $message,
        public ResponsePayload $payload,
        public array $errors,
    ) {
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'status_code' => $this->statusCode,
            'message' => $this->message,
            'payload' => $this->payload->toArray(),
            'errors' => $this->errors,
        ];
    }
}
