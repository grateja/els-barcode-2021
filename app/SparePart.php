<?php

namespace App;

use App\Traits\UsesOrders;
use App\Traits\UsesSerialProfiler;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SparePart extends Model
{
    use UsesUuid, SoftDeletes, UsesSerialProfiler, UsesOrders;

    protected $fillable = [
        'id', 'description', 'specs', 'supplier',
    ];

    public function getRedirectRoute() {
        return 'scan.spare-parts-profile';
    }

    public function getBarcodeLabel() {
        return $this->id . ' - ' . $this->description;
    }

    static function orders() {
        return [
            'part_number' => 'id',
            'description' => 'description',
            'specs' => 'specs',
        ];
    }

    public function sparePartItems() {
        return $this->hasMany('App\SparePartItem');
    }


}
