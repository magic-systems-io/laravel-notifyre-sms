<?php

namespace MagicSystemsIO\Notifyre\Facades;

use Illuminate\Support\Facades\Facade;

class Notifyre extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'notifyre';
    }
}
