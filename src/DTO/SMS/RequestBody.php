<?php

namespace MagicSystemsIO\Notifyre\DTO\SMS;

use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;

readonly class RequestBody implements Arrayable
{
    public ?string $sender;

    /**
     * @param string $body The body of the SMS message
     * @param Recipient[] $recipients A collection of recipient objects
     * @param ?string $sender The mobile phone number of who sent the SMS (empty for shared number)
     */
    public function __construct(
        public string $body,
        public array $recipients,
        ?string $sender = null,
    ) {
        $this->sender = $sender;
        if (empty(trim($body))) {
            throw new InvalidArgumentException('Body cannot be empty');
        }
        if (empty($recipients)) {
            throw new InvalidArgumentException('Recipients cannot be empty');
        }
    }

    public function toArray(): array
    {
        $recipients = array_map(fn (Recipient $recipient) => $recipient->toArray(), $this->recipients);

        $data = [
            'Body' => $this->body,
            'Recipients' => $recipients,
        ];

        if ($this->sender !== null && !empty(trim($this->sender))) {
            $data['From'] = $this->sender;
        }

        return $data;
    }
}
