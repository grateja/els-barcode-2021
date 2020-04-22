<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subdealer extends Model
{
    use UsesUuid, SoftDeletes;

    protected $fillable = [
        'subdealer_name', 'company_name', 'address',
    ];
}
