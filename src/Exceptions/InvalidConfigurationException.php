<?php

namespace Arbi\Notifyre\Exceptions;

use Exception;

class InvalidConfigurationException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message, 500);
    }
}
