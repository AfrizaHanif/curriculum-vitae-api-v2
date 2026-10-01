<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEducationRequest;
use App\Http\Requests\UpdateEducationRequest;
use App\Http\Resources\EducationResource;
use App\Models\Education;
use App\Traits\HandlesResourceFiltering;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class EducationController extends Controller implements HasMiddleware
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
        $query = $request->user()?->profile?->educations() ?? Education::query();

        match ($request->query('trashed')) {
            'with' => $query->withTrashed(),
            'only' => $query->onlyTrashed(),
            default => null,
        };

        $educations = $this->getFilteredResults($request, $query, '', null, 15);

        return EducationResource::collection($educations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEducationRequest $request): JsonResponse|EducationResource
    {
        // Get user profile data
        $userProfile = $request->user()?->profile;

        // Check user profile is exist or not
        if (! $userProfile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        // Get validated data
        $educationData = $request->validated();

        // Create education data
        $education = $userProfile->educations()->create($educationData);

        // Return as JSON
        return new EducationResource($education);
    }

    /**
     * Display the specified resource.
     */
    public function show(Education $education): EducationResource
    {
        // Return as JSON
        return new EducationResource($education);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEducationRequest $request, Education $education): EducationResource
    {
        // Get validated data
        $educationData = $request->validated();

        // Update education data
        $education->update($educationData);

        // Return as JSON
        return new EducationResource($education);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Education $education): Response
    {
        $this->authorize('delete', $education);

        $request->boolean('force') ? $education->forceDelete() : $education->delete();

        return response()->noContent();
    }

    /**
     * Restore the specified soft-deleted resource.
     */
    public function restore(string $id): EducationResource
    {
        // Find deleted education by ID
        $education = Education::onlyTrashed()->findOrFail($id);

        // Authorize user
        $this->authorize('restore', $education);

        // Restore education data
        $education->restore();

        // Return as JSON
        return new EducationResource($education);
    }
}
