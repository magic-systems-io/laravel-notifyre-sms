<?php

namespace MagicSystemsIO\Notifyre\DTO\SMS;

use Illuminate\Contracts\Support\Arrayable;

readonly class ResponseBody implements Arrayable
{
    /**
     * @param bool $success Shows if the API call has been successful
     * @param int $statusCode The status of the HTTP call
     * @param string $message A detailed message providing more information
     * @param ResponsePayload $payload Response data
     * @param array $errors Detailed error information if any
     */
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
