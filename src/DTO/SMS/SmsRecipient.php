<?php

namespace MagicSystemsIO\Notifyre\DTO\SMS;

use Illuminate\Contracts\Support\Arrayable;

readonly class SmsRecipient implements Arrayable
{
    public function __construct(
        public string $id,
        public string $friendlyID,
        public string $toNumber,
        public string $fromNumber,
        public float $cost,
        public int $messageParts,
        public float $costPerPart,
        public string $status,
        public string $statusMessage,
        public ?string $deliveryStatus,
        public int $queuedDateUtc,
        public int $completedDateUtc,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'friendly_id' => $this->friendlyID,
            'to_number' => $this->toNumber,
            'from_number' => $this->fromNumber,
            'cost' => $this->cost,
            'message_parts' => $this->messageParts,
            'cost_per_part' => $this->costPerPart,
            'status' => $this->status,
            'status_message' => $this->statusMessage,
            'delivery_status' => $this->deliveryStatus,
            'queued_date_utc' => $this->queuedDateUtc,
            'completed_date_utc' => $this->completedDateUtc,
        ];
    }
}
