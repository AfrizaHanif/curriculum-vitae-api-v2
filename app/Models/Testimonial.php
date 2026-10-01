<?php

namespace App\Models;

use Database\Factories\TestimonialFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Table('testimonials')]
#[Fillable([
    'id',
    'profile_id',
    'name',
    'role',
    'content',
])]
class Testimonial extends BaseAPI
{
    /** @use HasFactory<TestimonialFactory> */
    use HasFactory, SoftDeletes;

    // Custom Properties
    protected string $idPrefix = 'TES-'; // HasCustomId

    protected int $idPadding = 3;

    /**
     * @return BelongsTo<Profile, $this>
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
