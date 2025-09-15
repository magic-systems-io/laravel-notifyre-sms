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
            'sent' => $this->faker->boolean,
        ];
    }
}
