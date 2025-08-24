<?php

namespace MagicSystemsIO\Notifyre\Contracts;

interface NotifyreDriverFactoryInterface
{
    /**
     * Create and return a driver instance
     */
    public function create(): NotifyreDriverInterface;
}
