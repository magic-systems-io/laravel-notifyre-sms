<?php

namespace MagicSystemsIO\Notifyre\Providers;

use Illuminate\Support\ServiceProvider;
use MagicSystemsIO\Notifyre\Services\NotifyreService;

class ServicesServiceProvider extends ServiceProvider
{
    public array $singletons = [
        NotifyreService::class,
    ];
}
