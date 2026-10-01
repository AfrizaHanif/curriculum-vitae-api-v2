<?php

namespace App\Models;

use Database\Factories\SetupFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Table('setups')]
#[Fillable([
    'id',
    'profile_id',
    'name',
    'category',
    'description',
    'reason',
])]
class Setup extends BaseAPI
{
    /** @use HasFactory<SetupFactory> */
    use HasFactory, SoftDeletes;

    protected $casts = [
        'reason' => 'array',
    ];

    // Custom Properties
    protected string $idPrefix = 'SET-'; // HasCustomId

    protected int $idPadding = 3;

    /**
     * @return BelongsTo<Profile, $this>
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
