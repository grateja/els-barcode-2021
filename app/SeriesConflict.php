<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SeriesConflict extends Model
{
    public static function check($request, $field) {
        $profiler = SerialNumberProfiler::withTrashed()->find($request->{$field});
        if($profiler) {
            if($profiler->deleted_at != null && $request->replace) {
                return DB::transaction(function () use ($profiler) {
                    $profiler->forceDelete();
                    $profiler->deleteItem();
                    return false;
                });
            }
            // abort(422, 'This series is already in use');
            return response()->json([
                'errors' => [
                    $field => ['This series is already in used.'],
                    'replace' => ['*'],
                ]
            ], 422);
        }
        return false;
    }
}
