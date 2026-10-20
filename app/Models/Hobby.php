<?php

namespace App\Models;

use Database\Factories\HobbyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $profile_id
 * @property string $name
 * @property string|null $icon
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Profile $profile
 */
#[Table('hobbies')]
#[Fillable([
    'profile_id',
    'name',
    'icon',
])]
class Hobby extends BaseAPI
{
    /** @use HasFactory<HobbyFactory> */
    use HasFactory, SoftDeletes;

    protected string $idPrefix = 'HOB-';

    protected int $idPadding = 3;

    /**
     * @return BelongsTo<Profile, $this>
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
