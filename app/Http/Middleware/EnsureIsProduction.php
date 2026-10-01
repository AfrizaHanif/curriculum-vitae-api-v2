<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsProduction
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->isProduction()) {
            $isTestingLocal = app()->environment('local') && ($request->boolean('dry_run') || $request->boolean('validate_only'));

            if (! $isTestingLocal) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This action is only permitted in the production environment.',
                ], Response::HTTP_FORBIDDEN);
            }
        }

        return $next($request);
    }
}
