<?php

namespace App\Models;

use App\Traits\HasCustomId;
use App\Traits\HasFileCleanup;
use App\Traits\HasLogLabel;
use Illuminate\Database\Eloquent\Model;

/**
 * Base abstract model for curriculum vitae API resources.
 *
 * Provides non-incrementing string primary keys (custom prefixed IDs),
 * automatic storage file cleanup on deletion, and activity log labels.
 */
abstract class BaseAPI extends Model
{
    use HasCustomId, HasFileCleanup, HasLogLabel;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';
}
