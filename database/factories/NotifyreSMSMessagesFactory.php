<?php

namespace MagicSystemsIO\Notifyre\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use MagicSystemsIO\Notifyre\Models\NotifyreSmsMessages;

class NotifyreSMSMessagesFactory extends Factory
{
    protected $model = NotifyreSmsMessages::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'sender' => $this->faker->name,
            'body' => $this->faker->text,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
