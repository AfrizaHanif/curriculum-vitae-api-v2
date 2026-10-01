<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePortfolioRequest;
use App\Http\Requests\UpdatePortfolioRequest;
use App\Http\Resources\PortfolioResource;
use App\Models\Portfolio;
use App\Traits\HandlesFileUploads;
use App\Traits\HandlesResourceFiltering;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PortfolioController extends Controller implements HasMiddleware
{
    use AuthorizesRequests, HandlesFileUploads, HandlesResourceFiltering;

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
        $query = ($request->user()?->profile?->portfolios() ?? Portfolio::query())
            ->with(['features', 'caseStudies', 'expertises']);

        match ($request->query('trashed')) {
            'with' => $query->withTrashed(),
            'only' => $query->onlyTrashed(),
            default => null,
        };

        $portfolios = $this->getFilteredResults($request, $query, '', null, 15);

        return PortfolioResource::collection($portfolios);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePortfolioRequest $request): JsonResponse|PortfolioResource
    {
        $userProfile = $request->user()?->profile;

        if (! $userProfile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        $portfolioData = $request->validated();

        $customId = (new Portfolio)->generateCustomId();
        $idNum = preg_replace('/\D/', '', $customId);
        $baseCustomName = "IMG-{$idNum}_".time();
        $baseVideoName = "VID-{$idNum}_".time();

        $portfolioData['id'] = $customId;
        $portfolioData['slug'] = ! empty($portfolioData['slug']) ? $portfolioData['slug'] : Str::slug($portfolioData['title']);
        $portfolioData['image'] = $this->uploadFile($request, 'image', Portfolio::STORAGE_PATH, null, 'public', $baseCustomName);
        $portfolioData['gallery'] = $this->uploadFiles($request, 'gallery', Portfolio::STORAGE_PATH.'/gallery', [], 'public', $baseCustomName);

        if ($request->hasFile('video')) {
            $portfolioData['video'] = $this->uploadFile($request, 'video', Portfolio::STORAGE_PATH.'/videos', null, 'public', $baseVideoName);
        } elseif ($request->exists('video')) {
            $portfolioData['video'] = $request->input('video');
        }

        $portfolio = $userProfile->portfolios()->create($portfolioData);
        $portfolio->load(['features', 'caseStudies', 'expertises']);

        return new PortfolioResource($portfolio);
    }

    /**
     * Display the specified resource.
     */
    public function show(Portfolio $portfolio): PortfolioResource
    {
        $portfolio->loadMissing(['features', 'caseStudies', 'expertises']);

        return new PortfolioResource($portfolio);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePortfolioRequest $request, Portfolio $portfolio): PortfolioResource
    {
        $data = $request->validated();
        $idNum = preg_replace('/\D/', '', (string) $portfolio->id);
        $baseCustomName = "IMG-{$idNum}_".time();
        $baseVideoName = "VID-{$idNum}_".time();

        $data['image'] = $this->uploadFile(
            $request,
            'image',
            Portfolio::STORAGE_PATH,
            $portfolio->image,
            'public',
            $baseCustomName
        );

        $data['gallery'] = $this->updateGallery(
            $request,
            'gallery',
            Portfolio::STORAGE_PATH.'/gallery',
            $portfolio->gallery ?? [],
            'remaining_gallery',
            'public',
            $baseCustomName
        );

        if ($request->hasFile('video')) {
            $oldVideo = ($portfolio->video && ! filter_var($portfolio->video, FILTER_VALIDATE_URL)) ? $portfolio->video : null;
            $data['video'] = $this->uploadFile(
                $request,
                'video',
                Portfolio::STORAGE_PATH.'/videos',
                $oldVideo,
                'public',
                $baseVideoName
            );
        } elseif ($request->exists('video')) {
            $newVideo = $request->input('video');
            if ($portfolio->video && ! filter_var($portfolio->video, FILTER_VALIDATE_URL) && $portfolio->video !== $newVideo) {
                if (Storage::disk('public')->exists($portfolio->video)) {
                    Storage::disk('public')->delete($portfolio->video);
                }
            }
            $data['video'] = $newVideo;
        }

        $portfolio->update($data);
        $portfolio->load(['features', 'caseStudies', 'expertises']);

        return new PortfolioResource($portfolio);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Portfolio $portfolio): Response
    {
        $this->authorize('delete', $portfolio);

        $request->boolean('force') ? $portfolio->forceDelete() : $portfolio->delete();

        return response()->noContent();
    }

    /**
     * Restore the specified soft-deleted resource.
     */
    public function restore(string $id): PortfolioResource
    {
        $portfolio = Portfolio::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', $portfolio);

        $portfolio->restore();
        $portfolio->load(['features', 'caseStudies', 'expertises']);

        return new PortfolioResource($portfolio);
    }
}
