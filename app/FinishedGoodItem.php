<?php

namespace App;

use App\Traits\UsesSerialProfiler;
use App\Traits\UsesUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinishedGoodItem extends Model
{
    use UsesUuid, SoftDeletes, UsesSerialProfiler;

    protected $fillable = [
        'id', 'finished_good_id', 'client_id', 'subdealer_id', 'warehouse', 'status', 'current_location',
    ];

    public function getRedirectRoute() {
        return 'scan.finished-goods';
    }

    public function getDeletedAtAttribute($value) {
        if($value != null) {
            return Carbon::createFromDate($value)->format('y/M/D - h:i A');
        } else {
            return false;
        }
    }

    public static function findAll($id) {
        return static::with('finishedGood', 'subdealer', 'client', 'activityLogs')
            ->withTrashed()
            ->find($id);
    }

    public function finishedGood() {
        return $this->belongsTo('App\FinishedGood');
    }

    public function incomingFinishedGoodReports() {
        return $this->belongsToMany('App\IncomingFinishedGoodReport', 'incoming_f_g_report_items', 'finished_good_item_id', 'incoming_report_id');
    }

    public function outgoingFinishedGoodReports() {
        return $this->belongsToMany('App\OutgoingFinishedGoodReport', 'outgoing_f_g_report_items', 'finished_good_item_id', 'outgoing_report_id');
    }

    public function reservations() {
        return $this->belongsToMany('App\Reservation', 'reserved_finished_goods', 'finished_good_item_id', 'reservation_id');
    }

    public function incomingReport() {
        return $this->incomingFinishedGoodReports()->orderByDesc('created_at')->first();
    }

    public function outgoingReport() {
        return $this->outgoingFinishedGoodReports()->orderByDesc('created_at')->first();
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

    public function getModelAttribute() {
        return $this->finishedGood ? $this->finishedGood->id : '';
    }

    public function getDescriptionAttribute() {
        return $this->finishedGood ? $this->finishedGood->description : '';
    }
    public function getSpecsAttribute() {
        return $this->finishedGood ? $this->finishedGood->specs : '';
    }
    public function getSupplierAttribute() {
        return $this->finishedGood ? $this->finishedGood->supplier : '';
    }
}
