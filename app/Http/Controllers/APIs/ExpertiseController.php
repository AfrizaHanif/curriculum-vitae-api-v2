<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpertiseRequest;
use App\Http\Requests\UpdateExpertiseRequest;
use App\Http\Resources\ExpertiseResource;
use App\Models\Expertise;
use App\Traits\HandlesResourceFiltering;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ExpertiseController extends Controller implements HasMiddleware
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
        $query = ($request->user()?->profile?->expertises()->getQuery() ?? Expertise::query())->with('portfolios');

        match ($request->query('trashed')) {
            'with' => $query->withTrashed(),
            'only' => $query->onlyTrashed(),
            default => null,
        };

        $expertise = $this->getFilteredResults($request, $query, '', null, 15);

        return ExpertiseResource::collection($expertise);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExpertiseRequest $request): JsonResponse|ExpertiseResource
    {
        // Get user profile data
        $userProfile = $request->user()?->profile;

        // Check user profile is exist or not
        if (! $userProfile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        // Get validated data
        $validated = $request->validated();
        $portfolioIds = $validated['portfolio_ids'] ?? [];
        unset($validated['portfolio_ids']);

        // Create expertise data
        $expertise = $userProfile->expertises()->create($validated);

        if (! empty($portfolioIds)) {
            $expertise->portfolios()->sync($portfolioIds);
        }

        $expertise->load('portfolios');

        // Return as JSON
        return new ExpertiseResource($expertise);
    }

    /**
     * Display the specified resource.
     */
    public function show(Expertise $expertise): ExpertiseResource
    {
        $expertise->loadMissing('portfolios');

        // Return as JSON
        return new ExpertiseResource($expertise);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExpertiseRequest $request, Expertise $expertise): ExpertiseResource
    {
        $validated = $request->validated();

        if (array_key_exists('portfolio_ids', $validated)) {
            $expertise->portfolios()->sync($validated['portfolio_ids'] ?? []);
            unset($validated['portfolio_ids']);
        }

        // Update expertise data
        $expertise->update($validated);
        $expertise->load('portfolios');

        // Return as JSON
        return new ExpertiseResource($expertise);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Expertise $expertise): Response
    {
        $this->authorize('delete', $expertise);

        $request->boolean('force') ? $expertise->forceDelete() : $expertise->delete();

        return response()->noContent();
    }

    /**
     * Restore the specified soft-deleted resource.
     */
    public function restore(string $id): ExpertiseResource
    {
        // Find deleted skill by ID
        $expertise = Expertise::onlyTrashed()->findOrFail($id);

        // Authorize user
        $this->authorize('restore', $expertise);

        // Restore expertise data
        $expertise->restore();

        // Return as JSON
        return new ExpertiseResource($expertise);
    }
}
