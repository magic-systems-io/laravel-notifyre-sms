<?php

namespace Arbi\Notifyre\DTO\SMS;

use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;

readonly class RequestBodyDTO implements Arrayable
{
    /**
     * @param string $body The body of the SMS message
     * @param Recipient[] $recipients A collection of recipient objects
     * @param ?string $from The mobile phone number of who sent the SMS (empty for shared number)
     * @param ?int $scheduledDate Unix timestamp for scheduled sending
     * @param bool $addUnsubscribeLink Option to add opt-out link
     * @param ?string $callbackUrl Optional callback URL for SMS completion
     * @param array<string, string> $metadata Key-value pairs for additional information (max 50 keys)
     * @param ?string $campaignName Optional message reference
     */
    public function __construct(
        public string $body,
        public array $recipients,
        public ?string $from = null,
        public ?int $scheduledDate = null,
        public bool $addUnsubscribeLink = false,
        public ?string $callbackUrl = null,
        public array $metadata = [],
        public ?string $campaignName = null,
    ) {
        if (empty(trim($body))) {
            throw new InvalidArgumentException('Body cannot be empty');
        }
        if (empty($recipients)) {
            throw new InvalidArgumentException('Recipients cannot be empty');
        }
        if (count($metadata) > 50) {
            throw new InvalidArgumentException('Metadata cannot exceed 50 keys');
        }
        foreach ($metadata as $key => $value) {
            if (strlen($key) > 50) {
                throw new InvalidArgumentException('Metadata key cannot exceed 50 characters');
            }
            if (strlen($value) > 500) {
                throw new InvalidArgumentException('Metadata value cannot exceed 500 characters');
            }
        }
    }

    public function toArray(): array
    {
        $recipients = array_map(fn (Recipient $recipient) => $recipient->toArray(), $this->recipients);

        $data = [
            'Body' => $this->body,
            'Recipients' => $recipients,
        ];

        if ($this->from !== null && !empty(trim($this->from))) {
            $data['From'] = $this->from;
        }

        if (!empty($this->scheduledDate)) {
            $data['ScheduledDate'] = $this->scheduledDate;
        }

        if (!empty($this->addUnsubscribeLink)) {
            $data['AddUnsubscribeLink'] = $this->addUnsubscribeLink;
        }

        if ($this->callbackUrl !== null && !empty(trim($this->callbackUrl))) {
            $data['CallbackUrl'] = $this->callbackUrl;
        }

        if (!empty($this->metadata)) {
            $data['Metadata'] = $this->metadata;
        }

        if ($this->campaignName !== null && !empty(trim($this->campaignName))) {
            $data['CampaignName'] = $this->campaignName;
        }

        return $data;
    }
}
