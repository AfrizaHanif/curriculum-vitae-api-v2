<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExperienceRequest;
use App\Http\Requests\UpdateExperienceRequest;
use App\Http\Resources\ExperienceResource;
use App\Models\Experience;
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
        // Get user profile data
        $userProfile = $request->user()?->profile;

        // Check user profile is exist or not
        if (! $userProfile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        // Get validated data
        $experienceData = $request->validated();

        // Create experience data
        $experience = $userProfile->experiences()->create($experienceData);

        // Return as JSON
        return new ExperienceResource($experience);
    }

    /**
     * Display the specified resource.
     */
    public function show(Experience $experience): ExperienceResource
    {
        // Return as JSON
        return new ExperienceResource($experience);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExperienceRequest $request, Experience $experience): ExperienceResource
    {
        // Get validated data
        $experienceData = $request->validated();

        // Update experience data
        $experience->update($experienceData);

        // Return as JSON
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
        // Find deleted experience by ID
        $experience = Experience::onlyTrashed()->findOrFail($id);

        // Authorize user
        $this->authorize('restore', $experience);

        // Restore experience data
        $experience->restore();

        // Return as JSON
        return new ExperienceResource($experience);
    }
}
