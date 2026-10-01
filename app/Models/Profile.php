<?php

namespace App\Models;

use Database\Factories\ProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property int $user_id
 * @property string $fullname
 * @property string $phone
 * @property string|null $current_city
 * @property string|null $current_province
 * @property string $email
 * @property Carbon|string $birthday
 * @property string|null $tagline
 * @property string|null $description
 * @property string|null $philosophy
 * @property string $status
 * @property string|null $casual_photo
 * @property string|null $formal_photo
 * @property string|null $setup_image
 * @property string|null $resume
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read Collection<int, Skill> $skills
 */
#[Table('profiles')]
#[Fillable([
    'user_id',
    'fullname',
    'phone',
    'current_city',
    'current_province',
    'email',
    'birthday',
    'tagline',
    'description',
    'philosophy',
    'status',
    'casual_photo',
    'formal_photo',
    'setup_image',
    'resume',
])]
class Profile extends BaseAPI
{
    /** @use HasFactory<ProfileFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'tagline' => 'array',
            'description' => 'array',
            'philosophy' => 'array',
            'birthday' => 'date',
        ];
    }

    // Custom Properties
    protected string $idPrefix = 'PRO-'; // HasCustomId

    public const STORAGE_PATH = 'images';

    public const STORAGE_PDF_PATH = 'pdfs';

    public function getLogLabel(): string
    {
        return $this->fullname;
    }

    /**
     * @var array<int, string>
     */
    public array $fileAttributes = [
        'casual_photo',
        'formal_photo',
        'setup_image',
        'resume',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function educations(): HasMany
    {
        return $this->hasMany(Education::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class);
    }

    public function expertises(): HasMany
    {
        return $this->hasMany(Expertise::class);
    }

    public function hobbies(): HasMany
    {
        return $this->hasMany(Hobby::class);
    }

    public function setups(): HasMany
    {
        return $this->hasMany(Setup::class);
    }

    public function socials(): HasMany
    {
        return $this->hasMany(Social::class);
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function caseStudies(): HasManyThrough
    {
        return $this->hasManyThrough(CaseStudy::class, Portfolio::class);
    }
}
