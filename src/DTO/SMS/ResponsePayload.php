<?php

namespace MagicSystemsIO\Notifyre\DTO\SMS;

use Illuminate\Contracts\Support\Arrayable;

readonly class ResponsePayload implements Arrayable
{
    /**
     * @param string $smsMessageID The message ID of the SMS
     * @param string $friendlyID The human-friendly ID given to the SMS
     * @param InvalidNumber[] $invalidToNumbers Collection of invalid numbers and details
     */
    public function __construct(
        public string $smsMessageID,
        public string $friendlyID,
        public array $invalidToNumbers,
    ) {
    }

    public function toArray(): array
    {
        return [
            'sms_message_id' => $this->smsMessageID,
            'friendly_id' => $this->friendlyID,
            'invalid_to_numbers' => array_map(fn (InvalidNumber $invalidNumber) => $invalidNumber->toArray(), $this->invalidToNumbers),
        ];
    }
}
