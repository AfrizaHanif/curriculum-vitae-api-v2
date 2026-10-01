<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
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

class ProjectController extends Controller implements HasMiddleware
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
        $query = ($request->user()?->profile?->projects() ?? Project::query())
            ->with(['features', 'portfolio']);

        match ($request->query('trashed')) {
            'with' => $query->withTrashed(),
            'only' => $query->onlyTrashed(),
            default => null,
        };

        $projects = $this->getFilteredResults($request, $query, '', null, 15);

        return ProjectResource::collection($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request): JsonResponse|ProjectResource
    {
        $userProfile = $request->user()?->profile;

        if (! $userProfile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        $projectData = $request->validated();

        $customId = (new Project)->generateCustomId();
        $idNum = preg_replace('/\D/', '', $customId);
        $baseCustomName = "IMG-{$idNum}_".time();
        $baseVideoName = "VID-{$idNum}_".time();

        $projectData['id'] = $customId;
        $projectData['slug'] = ! empty($projectData['slug']) ? $projectData['slug'] : Str::slug($projectData['title']);
        $projectData['image'] = $this->uploadFile($request, 'image', Project::STORAGE_PATH, null, 'public', $baseCustomName);
        $projectData['gallery'] = $this->uploadFiles($request, 'gallery', Project::STORAGE_PATH.'/gallery', [], 'public', $baseCustomName);

        if ($request->hasFile('video')) {
            $projectData['video'] = $this->uploadFile($request, 'video', Project::STORAGE_PATH.'/videos', null, 'public', $baseVideoName);
        } elseif ($request->exists('video')) {
            $projectData['video'] = $request->input('video');
        }

        $project = $userProfile->projects()->create($projectData);
        $project->load(['features', 'portfolio']);

        return new ProjectResource($project);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): ProjectResource
    {
        $project->loadMissing(['features', 'portfolio']);

        return new ProjectResource($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project): ProjectResource
    {
        $data = $request->validated();
        $idNum = preg_replace('/\D/', '', (string) $project->id);
        $baseCustomName = "IMG-{$idNum}_".time();
        $baseVideoName = "VID-{$idNum}_".time();

        $data['image'] = $this->uploadFile(
            $request,
            'image',
            Project::STORAGE_PATH,
            $project->image,
            'public',
            $baseCustomName
        );

        $data['gallery'] = $this->updateGallery(
            $request,
            'gallery',
            Project::STORAGE_PATH.'/gallery',
            $project->gallery ?? [],
            'remaining_gallery',
            'public',
            $baseCustomName
        );

        if ($request->hasFile('video')) {
            $oldVideo = ($project->video && ! filter_var($project->video, FILTER_VALIDATE_URL)) ? $project->video : null;
            $data['video'] = $this->uploadFile(
                $request,
                'video',
                Project::STORAGE_PATH.'/videos',
                $oldVideo,
                'public',
                $baseVideoName
            );
        } elseif ($request->exists('video')) {
            $newVideo = $request->input('video');
            if ($project->video && ! filter_var($project->video, FILTER_VALIDATE_URL) && $project->video !== $newVideo) {
                if (Storage::disk('public')->exists($project->video)) {
                    Storage::disk('public')->delete($project->video);
                }
            }
            $data['video'] = $newVideo;
        }

        $project->update($data);
        $project->load(['features', 'portfolio']);

        return new ProjectResource($project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Project $project): Response
    {
        $this->authorize('delete', $project);

        $request->boolean('force') ? $project->forceDelete() : $project->delete();

        return response()->noContent();
    }

    /**
     * Restore the specified soft-deleted resource.
     */
    public function restore(string $id): ProjectResource
    {
        $project = Project::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', $project);

        $project->restore();
        $project->load(['features', 'portfolio']);

        return new ProjectResource($project);
    }
}
