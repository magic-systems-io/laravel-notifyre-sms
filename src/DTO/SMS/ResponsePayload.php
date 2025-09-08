<?php

namespace MagicSystemsIO\Notifyre\DTO\SMS;

use Illuminate\Contracts\Support\Arrayable;

readonly class ResponsePayload implements Arrayable
{
    /**
     * @param SmsRecipient[] $recipients Collection of recipients and their details
     * @param InvalidNumber[] $invalidToNumbers Collection of invalid numbers and details (for send response)
     */
    public function __construct(
        public string $id,
        public string $friendlyID,
        public string $accountID,
        public string $createdBy,
        public array $recipients,
        public string $status,
        public float $totalCost,
        public Metadata $metadata,
        public int $createdDateUtc,
        public int $submittedDateUtc,
        public ?int $completedDateUtc,
        public int $lastModifiedDateUtc,
        public string $campaignName,
        public array $invalidToNumbers = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'friendly_id' => $this->friendlyID,
            'account_id' => $this->accountID,
            'created_by' => $this->createdBy,
            'recipients' => array_map(fn (SmsRecipient $recipient) => $recipient->toArray(), $this->recipients),
            'status' => $this->status,
            'total_cost' => $this->totalCost,
            'metadata' => $this->metadata->toArray(),
            'created_date_utc' => $this->createdDateUtc,
            'submitted_date_utc' => $this->submittedDateUtc,
            'completed_date_utc' => $this->completedDateUtc,
            'last_modified_date_utc' => $this->lastModifiedDateUtc,
            'campaign_name' => $this->campaignName,
            'invalid_to_numbers' => array_map(fn (InvalidNumber $invalidNumber) => $invalidNumber->toArray(), $this->invalidToNumbers),
        ];
    }
}
