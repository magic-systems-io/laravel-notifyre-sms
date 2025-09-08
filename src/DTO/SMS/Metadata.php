<?php

namespace MagicSystemsIO\Notifyre\DTO\SMS;

use Illuminate\Contracts\Support\Arrayable;

readonly class Metadata implements Arrayable
{
    public function __construct(
        public string $requestingUserId,
        public string $requestingUserEmail,
    ) {
    }

    public function toArray(): array
    {
        return [
            'requesting_user_id' => $this->requestingUserId,
            'requesting_user_email' => $this->requestingUserEmail,
        ];
    }
}
