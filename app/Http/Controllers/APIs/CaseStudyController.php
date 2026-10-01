<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCaseStudyRequest;
use App\Http\Requests\UpdateCaseStudyRequest;
use App\Http\Resources\CaseStudyResource;
use App\Models\CaseStudy;
use App\Traits\HandlesResourceFiltering;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CaseStudyController extends Controller implements HasMiddleware
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
        $query = $request->user()?->profile?->caseStudies() ?? CaseStudy::query();

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
        // Get data
        $userProfile = $request->user()?->profile;

        // Check user profile
        if (! $userProfile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        // Get validated data
        $caseStudyData = $request->validated();

        $portfolio = $userProfile->portfolios()->findOrFail($caseStudyData['portfolio_id']);
        $caseStudy = $portfolio->caseStudies()->create($caseStudyData);

        return new CaseStudyResource($caseStudy);
    }

    /**
     * Display the specified resource.
     */
    public function show(CaseStudy $caseStudy): CaseStudyResource
    {
        // Return as JSON
        return new CaseStudyResource($caseStudy);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCaseStudyRequest $request, CaseStudy $caseStudy): CaseStudyResource
    {
        // Update case study data
        $caseStudy->update($request->validated());

        // Return as JSON
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
        // Find deleted case study by ID
        $caseStudy = CaseStudy::onlyTrashed()->findOrFail($id);

        // Authorize user
        $this->authorize('restore', $caseStudy);

        // Restore case study
        $caseStudy->restore();

        // Return as JSON
        return new CaseStudyResource($caseStudy);
    }
}
