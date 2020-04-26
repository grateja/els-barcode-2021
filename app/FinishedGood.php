<?php

namespace App;

use App\Traits\UsesOrders;
use App\Traits\UsesSerialProfiler;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinishedGood extends Model
{
    use UsesUuid, SoftDeletes, UsesSerialProfiler, UsesOrders;

    protected $fillable = [
        'id', 'description', 'specs', 'supplier',
    ];

    static function orders() {
        return [
            'model' => 'id',
            'description' => 'description',
            'scanned' => 'created_at',
        ];
    }

    public function getRedirectRoute() {
        return 'scan.finished-goods-profile';
    }

    public function getBarcodeLabel() {
        return $this->description;
    }

    public function finishedGoodItems() {
        return $this->hasMany('App\FinishedGoodItem');
    }
}
