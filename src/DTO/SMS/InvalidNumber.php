<?php

namespace MagicSystemsIO\Notifyre\DTO\SMS;

use Illuminate\Contracts\Support\Arrayable;

readonly class InvalidNumber implements Arrayable
{
    public function __construct(
        public string $number,
        public string $message,
    ) {
    }

    public function toArray(): array
    {
        return [
            'number' => $this->number,
            'message' => $this->message,
        ];
    }
}
