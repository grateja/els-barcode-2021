<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncomingSparePartReport extends Model
{
    use UsesUuid, SoftDeletes;

    public function sparePartItems() {
        return $this->belongsToMany('App\SparePartItem', 'incoming_s_p_report_items', 'incoming_report_id', 'spare_part_item_id');
    }
}
