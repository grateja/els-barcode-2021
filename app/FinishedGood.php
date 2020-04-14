<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinishedGood extends Model
{
    use UsesUuid, SoftDeletes;

    protected $fillable = [
        'model', 'description', 'specs', 'supplier',
    ];

    public function finishedGoodItems() {
        return $this->hasMany('App\FinishedGoodItem');
    }
}
