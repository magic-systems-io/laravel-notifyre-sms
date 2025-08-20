<?php

namespace Arbi\Notifyre\DTO\SMS;

use InvalidArgumentException;

class RequestBodyDTO
{
    /**
     * @param string $body
     * @param ?string $sender
     * @param Recipient[] $recipients
     */
    public function __construct(
        public string $body,
        public ?string $sender,
        public array $recipients,
    ) {
        if (empty(trim($body))) {
            throw new InvalidArgumentException('Body cannot be empty');
        }
        if (empty($recipients)) {
            throw new InvalidArgumentException('Recipients cannot be empty');
        }
    }
}
