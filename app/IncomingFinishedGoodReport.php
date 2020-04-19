<?php

namespace App;

use App\Traits\UsesUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncomingFinishedGoodReport extends Model
{
    use UsesUuid, SoftDeletes;

    protected $fillable = [
        'received_date', 'po_number', 'rr_number', 'pi_number', 'truck_number',
    ];

    public function finishedGoodItems() {
        return $this->belongsToMany('App\FinishedGoodItem', 'incoming_f_g_report_items', 'incoming_report_id', 'finished_good_item_id');
    }

    public function attachItems($ids) {
        $ids = is_array($ids) ? $ids : [$ids];

        $existingIds = $this->finishedGoodItems()
            ->whereIn('finished_good_item_id', $ids)
            ->pluck('finished_good_item_id')->toArray();

        $newIds = array_diff($ids, $existingIds);

        $this->finishedGoodItems()->attach($newIds);
        return $newIds;
    }

    public function getReceivedDateAttribute($value) {
        if($value) {
            return Carbon::createFromDate($value)->format('Y-m-d');
        }
    }
}
