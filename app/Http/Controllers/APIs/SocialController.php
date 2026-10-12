<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSocialRequest;
use App\Http\Requests\UpdateSocialRequest;
use App\Http\Resources\SocialResource;
use App\Models\Social;
use App\Traits\HandlesBulkTrash;
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
    use AuthorizesRequests, HandlesBulkTrash, HandlesResourceFiltering;

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
        // Scope to the authenticated user's profile if logged in; otherwise query all public socials
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
        $userProfile = $request->user()?->profile;

        if (! $userProfile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        $socialData = $request->validated();
        $social = $userProfile->socials()->create($socialData);

        return new SocialResource($social);
    }

    /**
     * Display the specified resource.
     */
    public function show(Social $social): SocialResource
    {
        return new SocialResource($social);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSocialRequest $request, Social $social): SocialResource
    {
        $social->update($request->validated());

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
        $social = Social::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', $social);

        $social->restore();

        return new SocialResource($social);
    }
}
