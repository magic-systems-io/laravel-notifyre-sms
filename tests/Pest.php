<?php

namespace MagicSystemsIO\Notifyre\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

uses(TestCase::class, RefreshDatabase::class)->in('Feature');
uses(TestCase::class, RefreshDatabase::class)->in('Unit');
