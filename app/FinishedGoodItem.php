<?php

namespace App;

use App\Traits\UsesUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinishedGoodItem extends Model
{
    use UsesUuid, SoftDeletes;

    protected $fillable = [
        'finished_good_id', 'serial_number', 'client_id', 'subdealer_id', 'warehouse', 'status', 'current_location',
    ];

    public function finishedGood() {
        return $this->belongsTo('App\FinishedGood');
    }

    public function incomingFinishedGoodReports() {
        return $this->belongsToMany('App\IncomingFinishedGoodReport', 'incoming_f_g_report_items', 'finished_good_item_id', 'incoming_report_id');
    }

    public function outgoingFinishedGoodReports() {
        return $this->belongsToMany('App\OutgoingFinishedGoodReport', 'outgoing_f_g_report_items', 'finished_good_item_id', 'outgoing_report_id');
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
        return $this->finishedGood ? $this->finishedGood->model : '';
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

    protected static function boot()
    {
        static::deleting(function($model) {
            $model->serial_number = "$model->serial_number[".Carbon::now()->toString()."]";
            $model->save();
        });
        parent::boot();
    }
}
