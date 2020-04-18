<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixedAssetRemarks extends Model
{
    use UsesUuid, SoftDeletes;

    protected $fillable = [
        'fixed_asset_id', 'content',
    ];
}
