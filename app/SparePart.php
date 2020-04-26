<?php

namespace App;

use App\Traits\UsesSerialProfiler;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SparePart extends Model
{
    use UsesUuid, SoftDeletes, UsesSerialProfiler;

    protected $fillable = [
        'id', 'description', 'specs', 'supplier',
    ];

    public function getRedirectRoute() {
        return 'scan.spare-parts-profile';
    }

    public function getBarcodeLabel() {
        return $this->id . ' - ' . $this->description;
    }

    public function sparePartItems() {
        return $this->hasMany('App\SparePartItem');
    }


}
