<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class Role extends Model
{
    use SoftDeletes, UsesUuid;

    public $timestamps = false;

    public function users() {
        return $this->belongsToMany('App\User', 'role_users', 'user_id', 'role_id');
    }
}
