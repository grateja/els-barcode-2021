<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncomingFinishedGoodReport extends Model
{
    use UsesUuid, SoftDeletes;

    public function finishedGoodItems() {
        return $this->belongsToMany('App\FinishedGoodItem', 'incoming_f_g_report_items', 'incoming_report_id', 'finished_good_item_id');
    }
}
