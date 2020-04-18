<?php

namespace App;

use App\Traits\UsesSerialProfiler;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinishedGood extends Model
{
    use UsesUuid, SoftDeletes, UsesSerialProfiler;

    protected $fillable = [
        'id', 'description', 'specs', 'supplier',
    ];

    public function getRedirectRoute() {
        return 'scan.finished-goods-profile';
    }

    public function finishedGoodItems() {
        return $this->hasMany('App\FinishedGoodItem');
    }
}
