<?php

namespace App\Models;

use Database\Factories\HobbyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Table('hobbies')]
#[Fillable([
    'id',
    'profile_id',
    'name',
    'icon',
])]
class Hobby extends BaseAPI
{
    /** @use HasFactory<HobbyFactory> */
    use HasFactory, SoftDeletes;

    // Custom Properties
    protected string $idPrefix = 'HOB-'; // HasCustomId

    protected int $idPadding = 3;

    /**
     * @return BelongsTo<Profile, $this>
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
