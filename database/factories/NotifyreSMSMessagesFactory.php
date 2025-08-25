<?php

namespace MagicSystemsIO\Notifyre\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use MagicSystemsIO\Notifyre\Models\NotifyreSMSMessages;

class NotifyreSMSMessagesFactory extends Factory
{
    protected $model = NotifyreSMSMessages::class;

    public function definition(): array
    {
        return [
            'messageId' => $this->faker->uuid,
            'sender' => $this->faker->name,
            'body' => $this->faker->text,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
