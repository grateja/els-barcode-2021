<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SparePart extends Model
{
    use UsesUuid, SoftDeletes;

    public function sparePartItems() {
        return $this->hasMany('App\SparePartItem');
    }
}
