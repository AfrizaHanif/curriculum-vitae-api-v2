<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCaseStudyRequest;
use App\Http\Requests\UpdateCaseStudyRequest;
use App\Http\Resources\CaseStudyResource;
use App\Models\CaseStudy;
use App\Models\Profile;
use App\Traits\HandlesBulkTrash;
use App\Traits\HandlesResourceFiltering;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CaseStudyController extends Controller implements HasMiddleware
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
        // Scope to the authenticated user's profile if logged in; otherwise query all public case studies
        $query = $request->user()?->profile?->caseStudies()->getQuery() ?? CaseStudy::query();

        match ($request->query('trashed')) {
            'with' => $query->withTrashed(),
            'only' => $query->onlyTrashed(),
            default => null,
        };

        $caseStudies = $this->getFilteredResults($request, $query, '', null, 15);

        return CaseStudyResource::collection($caseStudies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCaseStudyRequest $request): JsonResponse|CaseStudyResource
    {
        $userProfile = $request->user()?->profile;

        if (! $userProfile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        $caseStudyData = $request->validated();

        // Ensure the portfolio belongs to the user to prevent cross-account attachment (IDOR protection)
        $portfolio = $userProfile->portfolios()->findOrFail($caseStudyData['portfolio_id']);
        $caseStudy = $portfolio->caseStudies()->create($caseStudyData);

        return new CaseStudyResource($caseStudy);
    }

    /**
     * Display the specified resource.
     */
    public function show(CaseStudy $caseStudy): CaseStudyResource
    {
        return new CaseStudyResource($caseStudy);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCaseStudyRequest $request, CaseStudy $caseStudy): CaseStudyResource
    {
        $caseStudyData = $request->validated();

        if (isset($caseStudyData['portfolio_id']) && $caseStudyData['portfolio_id'] !== $caseStudy->portfolio_id) {
            $userProfile = $request->user()?->profile;
            if (! $userProfile || ! $userProfile->portfolios()->where('id', $caseStudyData['portfolio_id'])->exists()) {
                abort(404, 'Portfolio not found.');
            }
        }

        $caseStudy->update($caseStudyData);

        return new CaseStudyResource($caseStudy);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, CaseStudy $caseStudy): Response
    {
        $this->authorize('delete', $caseStudy);

        $request->boolean('force') ? $caseStudy->forceDelete() : $caseStudy->delete();

        return response()->noContent();
    }

    /**
     * Restore the specified soft-deleted resource.
     */
    public function restore(string $id): CaseStudyResource
    {
        $caseStudy = CaseStudy::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', $caseStudy);

        $caseStudy->restore();

        return new CaseStudyResource($caseStudy);
    }

    /**
     * Get the query for soft-deleted items belonging to the user's profile.
     */
    protected function getTrashedItemsQueryForProfile(Profile $userProfile): Builder|Relation
    {
        return CaseStudy::onlyTrashed()
            ->whereHas('portfolio', function ($query) use ($userProfile): void {
                $query->where('profile_id', $userProfile->id);
            });
    }
}
