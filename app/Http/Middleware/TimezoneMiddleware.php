<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TimezoneMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is logged in and has a timezone set
        if ($request->user()?->timezone) {
            config(['app.timezone' => $request->user()->timezone]);

            // Sync PHP's internal date functions with the user's choice
            date_default_timezone_set($request->user()->timezone);
        }

        return $next($request);
    }
}
