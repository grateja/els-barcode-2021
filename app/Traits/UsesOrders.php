<?php

namespace App\Traits;

trait UsesOrders {
    public static function canOrderBy($key) {
        return collect(static::orders())->filter(function($item, $_key) use ($key) {
            return $_key == $key;
        })->first();
    }
}
