<?php

namespace MagicSystemsIO\Notifyre\Database\Factories\Junction;

use Illuminate\Database\Eloquent\Factories\Factory;
use MagicSystemsIO\Notifyre\Models\JunctionTables\NotifyreSMSMessageRecipient;
use MagicSystemsIO\Notifyre\Models\NotifyreRecipients;
use MagicSystemsIO\Notifyre\Models\NotifyreSMSMessages;

class NotifyreSMSMessageRecipientFactory extends Factory
{
    protected $model = NotifyreSMSMessageRecipient::class;

    public function definition(): array
    {
        return [
            'recipient_id' => NotifyreRecipients::factory()->create()->getKey(),
            'sms_message_id' => NotifyreSMSMessages::factory()->create()->getKey(),
        ];
    }
}
