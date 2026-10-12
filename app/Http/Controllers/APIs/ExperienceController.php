<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExperienceRequest;
use App\Http\Requests\UpdateExperienceRequest;
use App\Http\Resources\ExperienceResource;
use App\Models\Experience;
use App\Traits\HandlesBulkTrash;
use App\Traits\HandlesResourceFiltering;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ExperienceController extends Controller implements HasMiddleware
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
        // Scope to the authenticated user's profile if logged in; otherwise query all public experiences
        $query = $request->user()?->profile?->experiences()->getQuery() ?? Experience::query();

        match ($request->query('trashed')) {
            'with' => $query->withTrashed(),
            'only' => $query->onlyTrashed(),
            default => null,
        };

        $experiences = $this->getFilteredResults($request, $query, '', null, 15);

        return ExperienceResource::collection($experiences);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExperienceRequest $request): JsonResponse|ExperienceResource
    {
        $userProfile = $request->user()?->profile;

        if (! $userProfile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        $experienceData = $request->validated();
        $experience = $userProfile->experiences()->create($experienceData);

        return new ExperienceResource($experience);
    }

    /**
     * Display the specified resource.
     */
    public function show(Experience $experience): ExperienceResource
    {
        return new ExperienceResource($experience);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExperienceRequest $request, Experience $experience): ExperienceResource
    {
        $experienceData = $request->validated();
        $experience->update($experienceData);

        return new ExperienceResource($experience);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Experience $experience): Response
    {
        $this->authorize('delete', $experience);

        $request->boolean('force') ? $experience->forceDelete() : $experience->delete();

        return response()->noContent();
    }

    /**
     * Restore the specified soft-deleted resource.
     */
    public function restore(string $id): ExperienceResource
    {
        $experience = Experience::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', $experience);

        $experience->restore();

        return new ExperienceResource($experience);
    }
}
