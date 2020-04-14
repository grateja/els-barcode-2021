<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class QueueItem extends Model
{
    use UsesUuid;

    protected $fillable = [
        'queue_id', 'code',
    ];

    public function queue() {
        return $this->belongsTo('App\Queue');
    }
}
