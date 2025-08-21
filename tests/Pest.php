<?php

namespace Arbi\Notifyre\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

uses(TestCase::class, RefreshDatabase::class)->in('Feature');

// Only Unit tests that specifically need Laravel
uses(TestCase::class, RefreshDatabase::class)->in('Unit/Services');
uses(TestCase::class, RefreshDatabase::class)->in('Unit/Models');
uses(TestCase::class, RefreshDatabase::class)->in('Unit/Providers/Core');
uses(TestCase::class, RefreshDatabase::class)->in('Unit/Providers/Features');
uses(TestCase::class, RefreshDatabase::class)->in('Unit/Providers/Infrastructure/ConfigurationServiceProviderTest.php');

uses(TestCase::class, RefreshDatabase::class)->in('Http');
