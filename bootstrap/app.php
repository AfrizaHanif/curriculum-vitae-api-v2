<?php

use App\Http\Middleware\EnsureIsProduction;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetNoCacheHeaders;
use App\Http\Middleware\TimezoneMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            TimezoneMiddleware::class,
        ]);
        $middleware->throttleApi();
        $middleware->statefulApi();
        $middleware->api(prepend: [
            SetNoCacheHeaders::class,
        ]);
        $middleware->alias([
            'production.only' => EnsureIsProduction::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // $exceptions->shouldRenderJsonWhen(
        //     fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        // );
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'You are not authorized to access this resource.',
                ], 401);
            }
        });
    })->create();
