<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Table('posts')]
#[Fillable([
    'id',
    'profile_id',
    'title',
    'slug',
    'category',
    'author',
    'tags',
    'summary',
    'image',
    'content',
    'is_featured',
])]
class Post extends BaseAPI
{
    /** @use HasFactory<PostFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_featured' => 'boolean',
        ];
    }

    // Custom Properties
    protected string $idPrefix = 'BLG-'; // HasCustomId

    public const STORAGE_PATH = 'images/blogs';

    public array $fileAttributes = ['image'];

    public bool $deleteFilesOnSoftDelete = false;

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
