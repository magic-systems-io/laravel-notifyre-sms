<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use MagicSystemsIO\Notifyre\Http\Middlewares\EnsureDatabaseIsEnabledMiddleware;

uses(RefreshDatabase::class);

it('allows request when database functionality is enabled', function () {
    config()->set('notifyre.database.enabled', true);

    Route::middleware(EnsureDatabaseIsEnabledMiddleware::class)->get('/notifyre-middleware-test', function () {
        return response('ok', 200);
    });

    $this->get('/notifyre-middleware-test')
        ->assertStatus(200)
        ->assertSee('ok');
});

it('aborts with 503 when database functionality is disabled', function () {
    config()->set('notifyre.database.enabled', false);

    Route::middleware(EnsureDatabaseIsEnabledMiddleware::class)->get('/notifyre-middleware-test', function () {
        return response('ok', 200);
    });

    $this->get('/notifyre-middleware-test')
        ->assertStatus(503);
});
