<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait UsesSynch {
    protected static function bootUsesSynch() {
        static::updating(function($model) {

            $model->synched = null;
            // if(!$model->getKey()) {
            //     $model->{$model->getKeyName()} = (string) Str::uuid();
            // }
        });

        static::deleting(function($model) {
            $model->synched = null;
            $model->save();
        });
    }
}
