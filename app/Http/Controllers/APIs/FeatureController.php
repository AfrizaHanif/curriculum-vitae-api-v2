<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeatureRequest;
use App\Http\Requests\UpdateFeatureRequest;
use App\Http\Resources\FeatureResource;
use App\Models\Feature;
use App\Traits\HandlesResourceFiltering;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FeatureController extends Controller implements HasMiddleware
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
        $query = Feature::query();

        match ($request->query('trashed')) {
            'with' => $query->withTrashed(),
            'only' => $query->onlyTrashed(),
            default => null,
        };

        $features = $this->getFilteredResults($request, $query, '', null, 15);

        return FeatureResource::collection($features);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFeatureRequest $request): JsonResponse|FeatureResource
    {
        // Get user profile data
        $userProfile = $request->user()?->profile;

        // Check user profile is exist or not
        if (! $userProfile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        // Get validated data
        $featureData = $request->validated();

        // Create feature data
        $feature = Feature::create($featureData);

        // Return as JSON
        return new FeatureResource($feature);
    }

    /**
     * Display the specified resource.
     */
    public function show(Feature $feature): FeatureResource
    {
        // Return as JSON
        return new FeatureResource($feature);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFeatureRequest $request, Feature $feature): FeatureResource
    {
        // Update feature data
        $feature->update($request->validated());

        // Return as JSON
        return new FeatureResource($feature);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Feature $feature): Response
    {
        $this->authorize('delete', $feature);

        $request->boolean('force') ? $feature->forceDelete() : $feature->delete();

        return response()->noContent();
    }

    /**
     * Restore the specified soft-deleted resource.
     */
    public function restore(string $id): FeatureResource
    {
        // Find deleted feature by ID
        $feature = Feature::onlyTrashed()->findOrFail($id);

        // Authorize user
        $this->authorize('restore', $feature);

        // Restore feature data
        $feature->restore();

        // Return as JSON
        return new FeatureResource($feature);
    }
}
