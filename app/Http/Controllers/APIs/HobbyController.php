<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHobbyRequest;
use App\Http\Requests\UpdateHobbyRequest;
use App\Http\Resources\HobbyResource;
use App\Models\Hobby;
use App\Traits\HandlesBulkTrash;
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
        // Scope to the authenticated user's profile if logged in; otherwise query all public hobbies
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
        $userProfile = $request->user()?->profile;

        if (! $userProfile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        $hobbyData = $request->validated();
        $hobby = $userProfile->hobbies()->create($hobbyData);

        return new HobbyResource($hobby);
    }

    /**
     * Display the specified resource.
     */
    public function show(Hobby $hobby): HobbyResource
    {
        return new HobbyResource($hobby);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHobbyRequest $request, Hobby $hobby): HobbyResource
    {
        $hobby->update($request->validated());

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
        $hobby = Hobby::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', $hobby);

        $hobby->restore();

        return new HobbyResource($hobby);
    }
}
