<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use App\Traits\HandlesFileUploads;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;

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

        // Scope to the authenticated user's profile if logged in; otherwise query all profiles
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
        return new ProfileResource($profile);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProfileRequest $request, Profile $profile): ProfileResource
    {
        $data = $request->validated();

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
        $currentResume = is_array($profile->resume) ? $profile->resume : [];

        // Handle localized resume file uploads (e.g. multiple language editions)
        if ($request->hasFile('resume')) {
            $resumeFiles = $request->file('resume');
            $resumeData = $currentResume;

            if (is_array($resumeFiles)) {
                foreach ($resumeFiles as $locale => $file) {
                    if ($file instanceof UploadedFile) {
                        $customName = 'CV_Muhammad_Afriza_Hanif_'.Str::upper((string) $locale);
                        $path = $this->uploadFile(
                            $request,
                            "resume.{$locale}",
                            Profile::STORAGE_PDF_PATH,
                            $currentResume[$locale] ?? null,
                            'public',
                            $customName,
                            false,
                        );
                        if ($path) {
                            $resumeData[$locale] = $path;
                        }
                    }
                }
                $data['resume'] = $resumeData;
            } elseif ($resumeFiles instanceof UploadedFile) {
                $path = $this->uploadFile(
                    $request,
                    'resume',
                    Profile::STORAGE_PDF_PATH,
                    $currentResume['en'] ?? null,
                    'public',
                    'CV_Muhammad_Afriza_Hanif_EN',
                    false,
                );
                if ($path) {
                    $resumeData['en'] = $path;
                }
                $data['resume'] = $resumeData;
            }
        } elseif ($request->exists('resume')) {
            if (is_array($request->input('resume'))) {
                $data['resume'] = $request->input('resume');
            } elseif ($request->input('resume') === null) {
                $data['resume'] = null;
            }
        } else {
            unset($data['resume']);
        }

        $profile->update($data);

        return new ProfileResource($profile);
    }
}
