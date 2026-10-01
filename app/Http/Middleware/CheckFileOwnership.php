<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckFileOwnership
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->route('path');

        if (! $path) {
            return $next($request);
        }

        // Security: Prevent directory traversal
        $cleanPath = str_replace('..', '', $path);
        $user = $request->user();

        $parts = explode('/', $cleanPath);
        $folder = $parts[0];

        if (! $user) {
            abort(403, 'Unauthorized access to this private file.');
        }

        $isOwner = match ($folder) {
            'profiles' => DB::table('profiles')
                ->where('user_id', $user->id)
                ->where(function ($query) use ($cleanPath): void {
                    $query->where('casual_photo', $cleanPath)
                        ->orWhere('formal_photo', $cleanPath)
                        ->orWhere('setup_image', $cleanPath)
                        ->orWhere('resume', $cleanPath);
                })
                ->exists(),
            'certificates' => DB::table('certificates')
                ->where('user_id', $user->id)
                ->where('file_path', $cleanPath)
                ->exists(),
            default => false,
        };

        if (! $isOwner) {
            abort(403, 'Unauthorized access to this private file.');
        }

        return $next($request);
    }
}
