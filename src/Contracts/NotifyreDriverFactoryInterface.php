<?php

namespace Arbi\Notifyre\Contracts;

interface NotifyreDriverFactoryInterface
{
    /**
     * Create and return a driver instance
     */
    public function create(): NotifyreDriverInterface;
}
