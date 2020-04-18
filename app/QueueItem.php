<?php

namespace App;

use App\Traits\UsesSerialProfiler;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QueueItem extends Model
{
    use UsesUuid, UsesSerialProfiler, SoftDeletes;

    protected $fillable = [
        'id', 'queue_id',
    ];

    public function getRedirectRoute() {
        return 'scan.queues';
    }

    public function queue() {
        return $this->belongsTo('App\Queue');
    }
}
