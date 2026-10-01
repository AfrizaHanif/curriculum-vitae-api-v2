<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Traits\HandlesFileUploads;
use App\Traits\HandlesResourceFiltering;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;

class PostController extends Controller implements HasMiddleware
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
        $query = $request->user()?->profile?->posts()->getQuery() ?? Post::query();

        match ($request->query('trashed')) {
            'with' => $query->withTrashed(),
            'only' => $query->onlyTrashed(),
            default => null,
        };

        $posts = $this->getFilteredResults($request, $query, '', null, 15);

        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): JsonResponse|PostResource
    {
        $userProfile = $request->user()?->profile;

        if (! $userProfile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        $postData = $request->validated();

        $customId = (new Post)->generateCustomId();
        $idNum = preg_replace('/\D/', '', $customId);
        $baseCustomName = "IMG-{$idNum}_".time();

        $postData['id'] = $customId;
        $postData['slug'] = ! empty($postData['slug']) ? $postData['slug'] : Str::slug($postData['title']);
        $postData['image'] = $this->uploadFile($request, 'image', Post::STORAGE_PATH, null, 'public', $baseCustomName);

        $post = $userProfile->posts()->create($postData);

        return new PostResource($post);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): PostResource
    {
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post): PostResource
    {
        $data = $request->validated();
        $idNum = preg_replace('/\D/', '', (string) $post->id);
        $baseCustomName = "IMG-{$idNum}_".time();

        $data['image'] = $this->uploadFile(
            $request,
            'image',
            Post::STORAGE_PATH,
            $post->image,
            'public',
            $baseCustomName
        );

        $post->update($data);

        return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Post $post): Response
    {
        $this->authorize('delete', $post);

        $request->boolean('force') ? $post->forceDelete() : $post->delete();

        return response()->noContent();
    }

    /**
     * Restore the specified soft-deleted resource.
     */
    public function restore(string $id): PostResource
    {
        $post = Post::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', $post);

        $post->restore();

        return new PostResource($post);
    }
}
