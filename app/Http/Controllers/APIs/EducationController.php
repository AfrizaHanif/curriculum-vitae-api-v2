<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEducationRequest;
use App\Http\Requests\UpdateEducationRequest;
use App\Http\Resources\EducationResource;
use App\Models\Education;
use App\Traits\HandlesBulkTrash;
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
        // Scope to the authenticated user's profile if logged in; otherwise query all public educations
        $query = $request->user()?->profile?->educations()->getQuery() ?? Education::query();

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
        $userProfile = $request->user()?->profile;

        if (! $userProfile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        $educationData = $request->validated();
        $education = $userProfile->educations()->create($educationData);

        return new EducationResource($education);
    }

    /**
     * Display the specified resource.
     */
    public function show(Education $education): EducationResource
    {
        return new EducationResource($education);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEducationRequest $request, Education $education): EducationResource
    {
        $educationData = $request->validated();
        $education->update($educationData);

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
        $education = Education::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', $education);

        $education->restore();

        return new EducationResource($education);
    }
}
