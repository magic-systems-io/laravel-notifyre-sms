<?php

namespace MagicSystemsIO\Notifyre\Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use MagicSystemsIO\Notifyre\Enums\NotifyreRecipientTypes;
use MagicSystemsIO\Notifyre\Models\NotifyreRecipients;

class NotifyreRecipientsFactory extends Factory
{
    protected $model = NotifyreRecipients::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'type' => $this->faker->randomElement(NotifyreRecipientTypes::values()),
            'value' => $this->faker->phoneNumber,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
