<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\PartsItem;
use App\ActivityLog;

class ActionsController extends Controller
{
    public function move($id, Request $request) {
        $rules = [
            'locationTo' => 'required',
            'remarks' => 'required',
            'specificLocation' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $id) {


                $partsItem = PartsItem::findOrFail($id);

                $partsItem->update([
                    'current_location' => $request->locationTo,
                    'specific_location' => $request->specificLocation,
                ]);

                ActivityLog::create([
                    'title' => 'Moved machine',
                    'remarks' => auth('api')->user()->fullname . " moved " . $partsItem->serial_number . " from {$request->locationFrom} to {$request->locationTo} ({$request->specificLocation})." . $request->remarks,
                    'tag' => 'move-machine',
                    'user_id' => auth()->id(),
                    'parts_item_id' => $partsItem->id,
                ]);

                // if($request->locationIdFrom == $request->locationIdTo && $partsItem->specific_location == $request->specificLocationTo) {
                //     return response()->json([
                //         'errors' => [
                //             'locationTo' => ['Cannot move to the same location']
                //         ]
                //     ], 422);
                // }


                // $partsItem->update([
                //     'specific_location' => $request->specificLocationTo,
                // ]);

                // Origin::insert([
                //     'parts_item_id' => $id,
                //     'location_id_from' => $request->locationIdFrom,
                //     'location_id_to' => $request->locationIdTo,
                //     'remarks' => $request->remarks,
                //     'user_id' => auth('api')->id(),
                // ]);

                // PartsItemLog::insert([
                //     'parts_item_id' => $id,
                //     'title' => 'Moved location (' . $partsItem->unique_code . ')',
                //     'user_id' => auth('api')->id(),
                //     'remarks' => $request->remarks,
                // ]);

                return response()->json([
                    'partsItem' => $partsItem,
                ], 200);
            });
        }
    }
}
