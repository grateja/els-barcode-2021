<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutgoingFinishedGoodReport extends Model
{
    use UsesUuid, SoftDeletes;

    protected $fillable = [
        'client_id', 'subdealer_id', 'reservation_id', 'date_delivered', 'po_date', 'downpayment_date', 'invoice_date', 'quotation_number', 'sales_invoice', 'dr_number', 'warranty_number', 'truck', 'driver',
    ];

    public function finishedGoodItems() {
        return $this->belongsToMany('App\FinishedGoodItem', 'outgoing_f_g_report_items', 'outgoing_report_id', 'finished_good_item_id');
    }

    public function client() {
        return $this->belongsTo('App\Client');
    }

    public function subdealer() {
        return $this->belongsTo('App\Subdealer');
    }

    public function reservation() {
        return $this->belongsTo('App\Reservation');
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
}
