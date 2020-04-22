<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SeriesConflict extends Model
{
    public static function check($request, $field) {
        $profiler = SerialNumberProfiler::find($request->{$field});
        if($profiler) {
            // abort(422, 'This series is already in use');
            return response()->json([
                'errors' => [
                    $field => ['This series is already in used']
                ]
            ], 422);
        }
        return false;
    }
}
