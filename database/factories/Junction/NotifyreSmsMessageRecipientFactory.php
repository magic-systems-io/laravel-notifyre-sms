<?php

namespace MagicSystemsIO\Notifyre\Database\Factories\Junction;

use Illuminate\Database\Eloquent\Factories\Factory;
use MagicSystemsIO\Notifyre\Models\JunctionTables\NotifyreSmsMessageRecipient;
use MagicSystemsIO\Notifyre\Models\NotifyreRecipients;
use MagicSystemsIO\Notifyre\Models\NotifyreSmsMessages;

class NotifyreSmsMessageRecipientFactory extends Factory
{
    protected $model = NotifyreSmsMessageRecipient::class;

    public function definition(): array
    {
        return [
            'recipient_id' => NotifyreRecipients::factory()->create()->id,
            'sms_message_id' => NotifyreSmsMessages::factory()->create()->id,
            'delivery_status' => 'pending',
        ];
    }

    /**
     * State for a delivered webhook callback
     */
    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'delivery_status' => 'delivered',
        ]);
    }

    /**
     * State for a sent webhook callback
     */
    public function sentToCarrier(): static
    {
        return $this->state(fn (array $attributes) => [
            'delivery_status' => 'sent',
        ]);
    }

    /**
     * State for an undeliverable webhook callback
     */
    public function undeliverable(): static
    {
        return $this->state(fn (array $attributes) => [
            'delivery_status' => 'undeliverable',
        ]);
    }
}
