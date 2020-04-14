<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    use UsesUuid;

    protected $fillable = [
        'name', 'user_id', 'done',
    ];

    public function queueItems() {
        return $this->hasMany('App\QueueItem');
    }
}
