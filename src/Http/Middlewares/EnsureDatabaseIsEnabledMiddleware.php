<?php

namespace MagicSystemsIO\Notifyre\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;

class EnsureDatabaseIsEnabledMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        abort_if(!config('notifyre.database.enabled'), 503, 'Notifyre database functionality is disabled.');

        return $next($request);
    }
}
