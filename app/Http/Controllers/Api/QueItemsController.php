<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\QueItem;
use Illuminate\Support\Facades\DB;
use App\PartsInfo;
use App\PartsItem;
use App\IncomingReport;
use App\Origin;
use Carbon\Carbon;
use App\PartsItemLog;

class QueItemsController extends Controller
{
    public function update($id, Request $request) {
        $queItem = QueItem::findOrFail($id);

        $rules = [
            'code' => 'required',
        ];
        if($request->validate($rules)) {
            $queItem->update([
                'code' => $request->code,
            ]);

            return response()->json([
                'queItem' => $queItem,
            ], 200);
        }
    }

    public function saveAll(Request $request) {
        $rules = [
            'partNumber' => 'required',
        ];
        if($request->validate($rules)) {

            return DB::transaction(function () use ($request) {
                $partsInfo = PartsInfo::where('code', $request->partNumber)->first();
                $incomingReport = IncomingReport::where('tracking_number', $request->trackingNumber)->first();

                if($partsInfo == null && !$request->description) {
                    return response()->json([
                        'errors' => [
                            'description' => ['Part item not found. Description is required to create new one.']
                        ]
                    ], 422);
                } else if($partsInfo == null) {
                    $partsInfo = PartsInfo::create([
                        'code' => $request->partNumber,
                        'description' => $request->description,
                        'specs' => $request->specs,
                        'model' => $request->model,
                        'item_type' => $request->itemType,
                        'supplierId' => $request->supplierId
                    ]);
                }

                if($incomingReport == null && $request->trackingNumber) {
                    $incomingReport = IncomingReport::create([
                        'date' => $request->date,
                        'tracking_number' => $request->trackingNumber,
                        'dr_number' => $request->DRNumber,
                        'rr_number' => $request->RRNumber,
                        'bl_number' => $request->BLNumber,
                        'pi_number' => $request->PINumber,
                        'bill_number' => $request->billNumber,
                        'truck_number' => $request->truckNumber,
                    ]);
                }

                foreach($request->queItems as $item) {
                    $partsItem = PartsItem::create([
                        'unique_code' => $item['code'],
                        'parts_info_id' => $partsInfo->id,
                        'serial_number' => $item['code'],
                        'incoming_report_id' => $incomingReport ? $incomingReport->id : null,
                        'specific_location' => $item['specificLocationTo'],
                    ]);

                    $origin = Origin::create([
                        'parts_item_id' => $partsItem->id,
                        'location_id_from' => $item['locationIdFrom'],
                        'location_id_to' => $item['locationIdTo'],
                        'user_id' => auth('api')->id(),
                        'date_moved' => Carbon::now(),
                    ]);

                    QueItem::find($item['id'])->update([
                        'done' => true,
                    ]);

                    PartsItemLog::insert([
                        'parts_item_id' => $partsItem->id,
                        'title' => 'Added a new item (' . $partsItem->unique_code . ')',
                        'user_id' => auth('api')->id(),
                    ]);
                }

            });

            return response()->json($request->queItems);
        }
    }
}
