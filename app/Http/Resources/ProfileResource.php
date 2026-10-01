<?php

namespace App\Http\Resources;

use App\Models\Profile;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin Profile
 */
class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return [
            'id' => $this->id,
            'fullname' => $this->fullname,
            'phone' => $this->phone,
            'current_city' => $this->current_city,
            'current_province' => $this->current_province,
            'email' => $this->email,
            'age' => $this->birthday?->age,
            'birthday' => $this->when($request->user() !== null, $this->birthday),
            'tagline' => $this->tagline,
            'description' => $this->description,
            'philosophy' => $this->philosophy,
            'status' => $this->status,
            'casual_photo' => $this->casual_photo ? $disk->url($this->casual_photo) : null,
            'formal_photo' => $this->formal_photo ? $disk->url($this->formal_photo) : null,
            'setup_image' => $this->setup_image ? $disk->url($this->setup_image) : null,
            'resume' => $this->resume ? $disk->url($this->resume) : null,
        ];
    }
}
