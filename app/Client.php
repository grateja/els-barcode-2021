<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use UsesUuid, SoftDeletes;

    protected $fillable = [
        'owner_name', 'shop_name', 'address',
    ];
}
