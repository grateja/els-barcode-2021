<?php

namespace App;

use App\Traits\UsesUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncomingSparePartReport extends Model
{
    use UsesUuid, SoftDeletes;

    protected $fillable = [
        'received_date', 'tracking_number', 'rr_number', 'pi_number', 'order_number',
    ];

    public function sparePartItems() {
        return $this->belongsToMany('App\SparePartItem', 'incoming_s_p_report_items', 'incoming_report_id', 'spare_part_item_id');
    }

    public function attachItems($ids) {
        $ids = is_array($ids) ? $ids : [$ids];

        $existingIds = $this->sparePartItems()
            ->whereIn('spare_part_item_id', $ids)
            ->pluck('spare_part_item_id')->toArray();

        $newIds = array_diff($ids, $existingIds);

        $this->sparePartItems()->attach($newIds);
        return $newIds;
    }

    public function getReceivedDateAttribute($value) {
        if($value) {
            return Carbon::createFromDate($value)->format('Y-m-d');
        }
    }
}
