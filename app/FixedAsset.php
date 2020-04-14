<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixedAsset extends Model
{
    use UsesUuid, SoftDeletes;

    public function account() {
        return $this->belongsTo('App\Account');
    }

    public function tags() {
        return $this->belongsToMany('App\Tag', 'fixed_asset_tags', 'fixed_asset_id', 'tag_id');
    }
}
