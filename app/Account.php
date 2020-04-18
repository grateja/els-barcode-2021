<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use UsesUuid, SoftDeletes;

    protected $fillable = [
        'id', 'name', 'department',
    ];

    public function fixedAssets() {
        return $this->hasMany('App\FixedAsset');
    }
}
