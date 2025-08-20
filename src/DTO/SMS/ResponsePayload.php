<?php

namespace Arbi\Notifyre\DTO\SMS;

class ResponsePayload
{
    public function __construct(
        public string $smsMessageID,
        public string $friendlyID,
        public array $invalidToNumbers,
    ) {
    }
}
