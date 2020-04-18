<?php

namespace App;

use App\Traits\UsesSerialProfiler;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixedAsset extends Model
{
    use UsesUuid, SoftDeletes, UsesSerialProfiler;

    protected $fillable = [
        'id', 'description', 'specs', 'account_id', 'created_at',
    ];

    public function account() {
        return $this->belongsTo('App\Account');
    }

    public function getRedirectRoute() {
        return 'scan.fixed-assets';
    }

    public function tags() {
        return $this->belongsToMany('App\Tag', 'fixed_asset_tags', 'fixed_asset_id', 'tag_id');
    }

    public function fixedAssetRemarks() {
        return $this->hasMany('App\FixedAssetRemarks');
    }

    public function getDateIssuedAttribute() {
        if($this->created_at) {
            return $this->created_at->format('Y-m-d');
        }
    }

    public function getAccountNameAttribute() {
        if($this->account) {
            return $this->account->name;
        }
    }

    public function getDepartmentAttribute() {
        if($this->account) {
            return $this->account->department;
        }
    }

    public function getTagsStrAttribute() {
        if(count($this->tags)) {
            $tagsStr = $this->tags->pluck('id')->toArray();
            return implode(', ', $tagsStr);
        }
    }
}
