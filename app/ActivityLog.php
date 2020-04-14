<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityLog extends Model
{
    use UsesUuid, SoftDeletes;

    protected $fillable = [
        'user_id', 'remarks', 'finished_good_item_id', 'spare_part_item_id', 'incoming_finished_good_report_item_id', 'outgoing_finished_good_report_item_id', 'incoming_spare_part_report_item_id', 'outgoing_spare_part_report_item_id', 'action',
    ];
}
