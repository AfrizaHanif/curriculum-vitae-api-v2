<?php

namespace App\Models;

use Database\Factories\CertificateFactory;
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
 * @property string $type
 * @property string|null $issuer
 * @property Carbon|string|null $issued_date
 * @property Carbon|string|null $expired_date
 * @property string|null $credential_id
 * @property string|null $credential_url
 * @property string|null $description
 * @property string|null $file
 * @property bool $is_featured
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Profile $profile
 */
#[Table('certificates')]
#[Fillable([
    'profile_id',
    'title',
    'type',
    'issuer',
    'issued_date',
    'expired_date',
    'credential_id',
    'credential_url',
    'description',
    'file',
    'is_featured',
])]
class Certificate extends BaseAPI
{
    /** @use HasFactory<CertificateFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'issued_date' => 'date',
            'expired_date' => 'date',
            'is_featured' => 'boolean',
        ];
    }

    // Custom Properties
    protected string $idPrefix = 'CER-'; // HasCustomId

    public const STORAGE_IMAGE_PATH = 'images/certificates';

    public const STORAGE_PDF_PATH = 'pdfs/certificates';

    /** @var array<int, string> */
    public array $fileAttributes = ['file'];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
