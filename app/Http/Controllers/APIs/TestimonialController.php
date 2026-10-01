<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestimonialRequest;
use App\Http\Requests\UpdateTestimonialRequest;
use App\Http\Resources\TestimonialResource;
use App\Models\Testimonial;
use App\Traits\HandlesResourceFiltering;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TestimonialController extends Controller implements HasMiddleware
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
        $query = $request->user()?->profile?->testimonials() ?? Testimonial::query();

        match ($request->query('trashed')) {
            'with' => $query->withTrashed(),
            'only' => $query->onlyTrashed(),
            default => null,
        };

        $testimonials = $this->getFilteredResults($request, $query, '', null, 15);

        return TestimonialResource::collection($testimonials);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTestimonialRequest $request): JsonResponse|TestimonialResource
    {
        // Get user profile data
        $userProfile = $request->user()?->profile;

        // Check user profile is exist or not
        if (! $userProfile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        // Get validated data
        $testimonialData = $request->validated();

        // Create testimonial data
        $testimonial = $userProfile->testimonials()->create($testimonialData);

        // Return as JSON
        return new TestimonialResource($testimonial);
    }

    /**
     * Display the specified resource.
     */
    public function show(Testimonial $testimonial): TestimonialResource
    {
        // Return as JSON
        return new TestimonialResource($testimonial);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTestimonialRequest $request, Testimonial $testimonial): TestimonialResource
    {
        // Update testimonial data
        $testimonial->update($request->validated());

        // Return as JSON
        return new TestimonialResource($testimonial);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Testimonial $testimonial): Response
    {
        $this->authorize('delete', $testimonial);

        $request->boolean('force') ? $testimonial->forceDelete() : $testimonial->delete();

        return response()->noContent();
    }

    /**
     * Restore the specified soft-deleted resource.
     */
    public function restore(string $id): TestimonialResource
    {
        // Find deleted testimonial by ID
        $testimonial = Testimonial::onlyTrashed()->findOrFail($id);

        // Authorize user
        $this->authorize('restore', $testimonial);

        // Restore testimonial data
        $testimonial->restore();

        // Return as JSON
        return new TestimonialResource($testimonial);
    }
}
