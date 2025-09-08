<?php

namespace MagicSystemsIO\Notifyre\DTO\SMS;

use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;

readonly class RequestBody implements Arrayable
{
    /**
     * @param Recipient[] $recipients A collection of recipient objects
     */
    public function __construct(
        public string $body,
        public array $recipients,
        public ?string $sender = null,
        public ?int $scheduledDate = null,
        public ?bool $addUnsubscribeLink = null,
        public ?string $callbackUrl = null,
        public ?array $metadata = null,
        public ?string $campaignName = null,
    ) {
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
            'From' => $this->sender,
            'ScheduledDate' => $this->scheduledDate,
            'AddUnsubscribeLink' => $this->addUnsubscribeLink,
            'CallbackUrl' => $this->callbackUrl,
            'Metadata' => $this->metadata,
            'CampaignName' => $this->campaignName,
        ];

        return array_filter($data, fn ($value) => $value !== null);
    }
}
