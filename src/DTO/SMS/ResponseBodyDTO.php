<?php

namespace Arbi\Notifyre\DTO\SMS;

class ResponseBodyDTO
{
    public function __construct(
        public bool $success,
        public int $statusCode,
        public string $message,
        public ResponsePayload $payload,
        public array $errors,
    ) {
    }
}
