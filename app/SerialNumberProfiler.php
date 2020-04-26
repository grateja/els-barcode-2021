<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SerialNumberProfiler extends Model
{
    use UsesUuid, SoftDeletes;

    protected $fillable = [
        'id', 'base_table', 'redirect', 'barcode_label',
    ];

    public static function findByCode($id) {
        return static::withTrashed()->find($id);
    }
}
