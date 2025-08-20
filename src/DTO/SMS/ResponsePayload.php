<?php

namespace Arbi\Notifyre\DTO\SMS;

readonly class ResponsePayload
{
    public function __construct(
        public string $smsMessageID,
        public string $friendlyID,
        public array $invalidToNumbers,
    ) {
    }
}
