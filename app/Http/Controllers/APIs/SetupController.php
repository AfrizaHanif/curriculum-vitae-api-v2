<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSetupRequest;
use App\Http\Requests\UpdateSetupRequest;
use App\Http\Resources\SetupResource;
use App\Models\Setup;
use App\Traits\HandlesBulkTrash;
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
        // Scope to the authenticated user's profile if logged in; otherwise query all public setups
        $query = $request->user()?->profile?->setups()->getQuery() ?? Setup::query();

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
        $userProfile = $request->user()?->profile;

        if (! $userProfile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        $setupData = $request->validated();
        $setup = $userProfile->setups()->create($setupData);

        return new SetupResource($setup);
    }

    /**
     * Display the specified resource.
     */
    public function show(Setup $setup): SetupResource
    {
        return new SetupResource($setup);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSetupRequest $request, Setup $setup): SetupResource
    {
        $setup->update($request->validated());

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
        $setup = Setup::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', $setup);

        $setup->restore();

        return new SetupResource($setup);
    }
}
