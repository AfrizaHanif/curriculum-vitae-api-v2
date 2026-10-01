<?php

namespace App\Models;

use App\Traits\HasCustomId;
use App\Traits\HasFileCleanup;
use App\Traits\HasLogLabel;
use Illuminate\Database\Eloquent\Model;

abstract class BaseAPI extends Model
{
    // Traits used by model (Can be found in app/Traits)
    use HasCustomId, HasFileCleanup, HasLogLabel;

    // Details of model
    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';
}
