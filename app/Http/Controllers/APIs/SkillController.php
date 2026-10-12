<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSkillRequest;
use App\Http\Requests\UpdateSkillRequest;
use App\Http\Resources\SkillResource;
use App\Models\Skill;
use App\Traits\HandlesBulkTrash;
use App\Traits\HandlesResourceFiltering;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SkillController extends Controller implements HasMiddleware
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
        // Scope to the authenticated user's profile if logged in; otherwise query all public skills
        $query = $request->user()?->profile?->skills()->getQuery() ?? Skill::query();

        match ($request->query('trashed')) {
            'with' => $query->withTrashed(),
            'only' => $query->onlyTrashed(),
            default => null,
        };

        $skills = $this->getFilteredResults($request, $query, '', null, 15);

        return SkillResource::collection($skills);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSkillRequest $request): JsonResponse|SkillResource
    {
        $userProfile = $request->user()?->profile;

        if (! $userProfile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        $skillData = $request->validated();
        $skill = $userProfile->skills()->create($skillData);

        return new SkillResource($skill);
    }

    /**
     * Display the specified resource.
     */
    public function show(Skill $skill): SkillResource
    {
        return new SkillResource($skill);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSkillRequest $request, Skill $skill): SkillResource
    {
        $skill->update($request->validated());

        return new SkillResource($skill);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Skill $skill): Response
    {
        $this->authorize('delete', $skill);

        $request->boolean('force') ? $skill->forceDelete() : $skill->delete();

        return response()->noContent();
    }

    /**
     * Restore the specified soft-deleted resource.
     */
    public function restore(string $id): SkillResource
    {
        $skill = Skill::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', $skill);

        $skill->restore();

        return new SkillResource($skill);
    }
}
