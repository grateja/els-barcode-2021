<?php

namespace App;

use App\Traits\UsesSerialProfiler;
use App\Traits\UsesUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SparePartItem extends Model
{
    use UsesUuid, SoftDeletes, UsesSerialProfiler;

    protected $fillable = [
        'id', 'spare_part_id', 'client_id', 'subdealer_id', 'warehouse', 'status', 'current_location',
    ];

    public function getRedirectRoute() {
        return 'scan.spare-parts';
    }

    public static function findAll($id) {
        return static::with('sparePart', 'subdealer', 'client', 'activityLogs')
            ->withTrashed()
            ->find($id);
    }

    public function getDeletedAtAttribute($value) {
        if($value != null) {
            return Carbon::createFromDate($value)->format('y/M/D - h:i A');
        } else {
            return false;
        }
    }

    public function sparePart() {
        return $this->belongsTo('App\SparePart');
    }

    public function incomingSparePartReports() {
        return $this->belongsToMany('App\IncomingSparePartReport', 'incoming_s_p_report_items', 'spare_part_item_id', 'incoming_report_id');
    }

    public function outgoingSparePartReports() {
        return $this->belongsToMany('App\OutgoingSparePartReport', 'outgoing_s_p_report_items', 'spare_part_item_id', 'outgoing_report_id');
    }

    public function incomingReport() {
        return $this->incomingSparePartReports()->orderByDesc('created_at')->first();
    }

    public function outgoingReport() {
        return $this->outgoingSparePartReports()->orderByDesc('created_at')->first();
    }

    public function activityLogs() {
        return $this->hasMany('App\ActivityLog')->orderByDesc('created_at');
    }

    public function client() {
        return $this->belongsTo('App\Client');
    }

    public function subdealer() {
        return $this->belongsTo('App\Subdealer');
    }

    public function getPartNumberAttribute() {
        return $this->sparePart ? $this->sparePart->id : '';
    }

    public function getDescriptionAttribute() {
        return $this->sparePart ? $this->sparePart->description : '';
    }
    public function getSpecsAttribute() {
        return $this->sparePart ? $this->sparePart->specs : '';
    }
    public function getSupplierAttribute() {
        return $this->sparePart ? $this->sparePart->supplier : '';
    }
}
