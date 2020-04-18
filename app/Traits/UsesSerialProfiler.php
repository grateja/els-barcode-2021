<?php

namespace App\Traits;

use App\SerialNumberProfiler;

trait UsesSerialProfiler {
    protected static function bootUsesSerialProfiler() {
        static::creating(function($model) {
            SerialNumberProfiler::create([
                'id' => $model->id,
                'base_table' => $model->getTable(),
                'redirect' => $model->getRedirectRoute(),
            ]);
        });

        static::deleting(function($model) {
            $profiler = SerialNumberProfiler::findByCode($model->id);
            if($model->forceDeleting === true) {
                $profiler->forceDelete();
            } else {
                $profiler->delete();
            }
        });

        static::updating(function($model) {
            $originalSeries = $model->getOriginal('id');
            $profiler = SerialNumberProfiler::findByCode($originalSeries);
            if($profiler) {
                $profiler->update([
                    'id' => $model->id,
                ]);
            }
        });

        static::restoring(function($model) {
            $profiler = SerialNumberProfiler::findByCode($model->id);
            $profiler->restore();
        });
    }
}
