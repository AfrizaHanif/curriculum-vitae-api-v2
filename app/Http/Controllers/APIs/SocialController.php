<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSocialRequest;
use App\Http\Requests\UpdateSocialRequest;
use App\Http\Resources\SocialResource;
use App\Models\Social;
use App\Traits\HandlesResourceFiltering;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SocialController extends Controller implements HasMiddleware
{
    use AuthorizesRequests, HandlesResourceFiltering;

    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = $request->user()?->profile?->socials()->getQuery() ?? Social::query();

        match ($request->query('trashed')) {
            'with' => $query->withTrashed(),
            'only' => $query->onlyTrashed(),
            default => null,
        };

        $socials = $this->getFilteredResults($request, $query, '', null, 15);

        return SocialResource::collection($socials);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSocialRequest $request): JsonResponse|SocialResource
    {
        // Get user profile data
        $userProfile = $request->user()?->profile;

        // Check user profile is exist or not
        if (! $userProfile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        // Get validated data
        $socialData = $request->validated();

        // Create social data
        $social = $userProfile->socials()->create($socialData);

        // Return as JSON
        return new SocialResource($social);
    }

    /**
     * Display the specified resource.
     */
    public function show(Social $social): SocialResource
    {
        // Return as JSON
        return new SocialResource($social);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSocialRequest $request, Social $social): SocialResource
    {
        // Update social data
        $social->update($request->validated());

        // Return as JSON
        return new SocialResource($social);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Social $social): Response
    {
        $this->authorize('delete', $social);

        $request->boolean('force') ? $social->forceDelete() : $social->delete();

        return response()->noContent();
    }

    /**
     * Restore the specified soft-deleted resource.
     */
    public function restore(string $id): SocialResource
    {
        // Find deleted social by ID
        $social = Social::onlyTrashed()->findOrFail($id);

        // Authorize user
        $this->authorize('restore', $social);

        // Restore social data
        $social->restore();

        // Return as JSON
        return new SocialResource($social);
    }
}
