<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SparePartItem extends Model
{
    use UsesUuid, SoftDeletes;

    public function sparePart() {
        return $this->belongsTo('App\SparePart');
    }

    public function incomingSparePartReports() {
        return $this->belongsToMany('App\IncomingSparePartReport', 'incoming_s_p_report_items', 'spare_part_item_id', 'incoming_report_id');
    }

    public function outgoingSparePartReports() {
        return $this->belongsToMany('App\OutgoingSparePartReport', 'outgoing_s_p_report_items', 'spare_part_item_id', 'outgoing_report_id');
    }
}
