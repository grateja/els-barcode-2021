<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use UsesUuid;

    protected $fillable = [
        'id'
    ];

    public function fixedAssets() {
        return $this->belongsToMany('App\FixedAsset', 'fixed_asset_tags', 'tag_id', 'fixed_asset_id');
    }
}
