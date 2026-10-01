<?php

namespace App\Models;

use Database\Factories\SocialFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Table('socials')]
#[Fillable([
    'id',
    'profile_id',
    'name',
    'url',
    'icon',
])]
class Social extends BaseAPI
{
    /** @use HasFactory<SocialFactory> */
    use HasFactory, SoftDeletes;

    // Custom Properties
    protected string $idPrefix = 'SOC-'; // HasCustomId

    protected int $idPadding = 3;

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
