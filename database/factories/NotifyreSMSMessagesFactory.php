<?php

namespace Database\Factories;

use Arbi\Notifyre\Models\NotifyreSMSMessages;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

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
