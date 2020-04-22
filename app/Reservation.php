<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use UsesUuid, SoftDeletes;

    protected $fillable = [
        'downpayment_date', 'reference_number', 'client_id', 'subdealer_id', 'remarks',
    ];

    public function client() {
        return $this->belongsTo('App\Client');
    }

    public function subdealer() {
        return $this->belongsTo('App\Subdealer');
    }

    public function finishedGoods() {
        return $this->belongsToMany('App\FinishedGoodItem', 'reserved_finished_goods', 'reservation_id', 'finished_good_item_id');
    }

    public function spareParts() {
        return $this->belongsToMany('App\SparePartItem', 'reserved_spare_parts', 'reservation_id', 'spare_part_item_id');
    }

    public function getOwnerNameAttribute() {
        if($this->client) {
            return $this->client->owner_name;
        }
        return null;
    }

    public function getSubdealerNameAttribute() {
        if($this->subdealer) {
            return $this->subdealer->subdealer_name;
        }
        return null;
    }

    public function attachFinishedGoods($ids) {
        $ids = is_array($ids) ? $ids : [$ids];

        $existingIds = $this->finishedGoods()
            ->whereIn('finished_good_item_id', $ids)
            ->pluck('finished_good_item_id')->toArray();

        $newIds = array_diff($ids, $existingIds);

        $this->finishedGoods()->attach($newIds);
        return $newIds;
    }

    public function attachSpareParts($ids) {
        $ids = is_array($ids) ? $ids : [$ids];

        $existingIds = $this->spareParts()
            ->whereIn('spare_part_item_id', $ids)
            ->pluck('spare_part_item_id')->toArray();

        $newIds = array_diff($ids, $existingIds);

        $this->spareParts()->attach($newIds);
        return $newIds;
    }
}
