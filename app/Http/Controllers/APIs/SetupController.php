<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSetupRequest;
use App\Http\Requests\UpdateSetupRequest;
use App\Http\Resources\SetupResource;
use App\Models\Setup;
use App\Traits\HandlesResourceFiltering;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SetupController extends Controller implements HasMiddleware
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
        $query = $request->user()?->profile?->setups() ?? Setup::query();

        match ($request->query('trashed')) {
            'with' => $query->withTrashed(),
            'only' => $query->onlyTrashed(),
            default => null,
        };

        $setup = $this->getFilteredResults($request, $query, '', null, 15);

        return SetupResource::collection($setup);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSetupRequest $request): JsonResponse|SetupResource
    {
        // Get user profile data
        $userProfile = $request->user()?->profile;

        // Check user profile is exist or not
        if (! $userProfile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        // Get validated data
        $setupData = $request->validated();

        // Create setup data
        $setup = $userProfile->setups()->create($setupData);

        // Return as JSON
        return new SetupResource($setup);
    }

    /**
     * Display the specified resource.
     */
    public function show(Setup $setup): SetupResource
    {
        // Return as JSON
        return new SetupResource($setup);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSetupRequest $request, Setup $setup): SetupResource
    {
        // Update setup data
        $setup->update($request->validated());

        // Return as JSON
        return new SetupResource($setup);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Setup $setup): Response
    {
        $this->authorize('delete', $setup);

        $request->boolean('force') ? $setup->forceDelete() : $setup->delete();

        return response()->noContent();
    }

    /**
     * Restore the specified soft-deleted resource.
     */
    public function restore(string $id): SetupResource
    {
        // Find deleted setup by ID
        $setup = Setup::onlyTrashed()->findOrFail($id);

        // Authorize user
        $this->authorize('restore', $setup);

        // Restore setup data
        $setup->restore();

        // Return as JSON
        return new SetupResource($setup);
    }
}
