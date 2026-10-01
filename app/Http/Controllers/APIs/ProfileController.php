<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use App\Traits\HandlesFileUploads;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProfileController extends Controller implements HasMiddleware
{
    use HandlesFileUploads;

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
        $user = $request->user();

        $query = Profile::query();

        if ($user?->profile?->id) {
            $query->where('id', $user->profile->id);
        }

        $profiles = $query->get();

        return ProfileResource::collection($profiles);
    }

    /**
     * Display the specified resource.
     */
    public function show(Profile $profile): ProfileResource
    {
        // Return as JSON
        return new ProfileResource($profile);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProfileRequest $request, Profile $profile): ProfileResource
    {
        // Get validated data
        $data = $request->validated();

        // Upload files if exist
        // Guide: $this->uploadFile(request, key, folder, oldPath, disk, customFilename, slug)

        $data['casual_photo'] = $this->uploadFile(
            $request,
            'casual_photo',
            Profile::STORAGE_PATH,
            $profile->casual_photo,
            'public',
            'profile_casual',
        );
        $data['formal_photo'] = $this->uploadFile(
            $request,
            'formal_photo',
            Profile::STORAGE_PATH,
            $profile->formal_photo,
            'public',
            'profile_formal',
        );
        $data['setup_image'] = $this->uploadFile(
            $request,
            'setup_image',
            Profile::STORAGE_PATH,
            $profile->setup_image,
            'public',
            'profile_setup',
        );
        $data['resume'] = $this->uploadFile(
            $request,
            'resume',
            Profile::STORAGE_PDF_PATH,
            $profile->resume,
            'public',
            'CV_Muhammad_Afriza_Hanif',
            false,
        );

        // Update profile data
        $profile->update($data);

        // Return as JSON
        return new ProfileResource($profile);
    }
}
