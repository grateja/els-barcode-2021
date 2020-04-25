<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutgoingSparePartReport extends Model
{
    use UsesUuid, SoftDeletes;

    protected $fillable = [
        'client_id', 'subdealer_id', 'reservation_id', 'date_delivered', 'po_date', 'downpayment_date', 'invoice_date', 'quotation_number', 'sales_invoice', 'dr_number', 'warranty_number',
    ];

    public function sparePartItems() {
        return $this->belongsToMany('App\SparePartItem', 'outgoing_s_p_report_items', 'outgoing_report_id', 'spare_part_item_id');
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

        $existingIds = $this->sparePartItems()
            ->whereIn('spare_part_item_id', $ids)
            ->pluck('spare_part_item_id')->toArray();

        $newIds = array_diff($ids, $existingIds);

        $this->sparePartItems()->attach($newIds);
        return $newIds;
    }
}
