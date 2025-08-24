<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use MagicSystemsIO\Notifyre\Models\NotifyreSMSMessages;

class NotifyreSMSMessagesFactory extends Factory
{
    protected $model = NotifyreSMSMessages::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(), //
            'updated_at' => Carbon::now(),
        ];
    }
}
