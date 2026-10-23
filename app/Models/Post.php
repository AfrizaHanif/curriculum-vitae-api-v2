<?php

namespace App\Models;

use App\Enums\PostCategory;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $profile_id
 * @property string $title
 * @property string $slug
 * @property PostCategory $category
 * @property string $author
 * @property array|null $tags
 * @property string $summary
 * @property string|null $image
 * @property string $content
 * @property bool $is_featured
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Profile $profile
 */
#[Table('posts')]
#[Fillable([
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
            'category' => PostCategory::class,
            'tags' => 'array',
            'is_featured' => 'boolean',
        ];
    }

    protected string $idPrefix = 'BLG-';

    public const STORAGE_PATH = 'images/blogs';

    public array $fileAttributes = ['image'];

    public bool $deleteFilesOnSoftDelete = false;

    /**
     * @return BelongsTo<Profile, $this>
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
