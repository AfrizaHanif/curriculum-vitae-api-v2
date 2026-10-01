<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHobbyRequest;
use App\Http\Requests\UpdateHobbyRequest;
use App\Http\Resources\HobbyResource;
use App\Models\Hobby;
use App\Traits\HandlesResourceFiltering;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class HobbyController extends Controller implements HasMiddleware
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
        $query = $request->user()?->profile?->hobbies()->getQuery() ?? Hobby::query();

        match ($request->query('trashed')) {
            'with' => $query->withTrashed(),
            'only' => $query->onlyTrashed(),
            default => null,
        };

        $hobbies = $this->getFilteredResults($request, $query, '', null, 15);

        return HobbyResource::collection($hobbies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHobbyRequest $request): JsonResponse|HobbyResource
    {
        // Get user profile
        $userProfile = $request->user()?->profile;

        // Check user profile is exist or not
        if (! $userProfile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        // Validate request
        $hobbyData = $request->validated();

        // Create hobby
        $hobby = $userProfile->hobbies()->create($hobbyData);

        // Return as JSON
        return new HobbyResource($hobby);
    }

    /**
     * Display the specified resource.
     */
    public function show(Hobby $hobby): HobbyResource
    {
        // Return as JSON
        return new HobbyResource($hobby);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHobbyRequest $request, Hobby $hobby): HobbyResource
    {
        // Update hobby
        $hobby->update($request->validated());

        // Return as JSON
        return new HobbyResource($hobby);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Hobby $hobby): Response
    {
        $this->authorize('delete', $hobby);

        $request->boolean('force') ? $hobby->forceDelete() : $hobby->delete();

        return response()->noContent();
    }

    /**
     * Restore the specified soft-deleted resource.
     */
    public function restore(string $id): HobbyResource
    {
        // Find deleted hobby by ID
        $hobby = Hobby::onlyTrashed()->findOrFail($id);

        // Authorize user
        $this->authorize('restore', $hobby);

        // Restore hobby
        $hobby->restore();

        // Return as JSON
        return new HobbyResource($hobby);
    }
}
