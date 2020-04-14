<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PartsInfo;
use App\PartsItem;
use Illuminate\Support\Facades\DB;
use App\Origin;
use Carbon\Carbon;
use App\PartsItemLog;
use App\PartsInfoLog;
use App\ActivityLog;

class PartsItemsController extends Controller
{
    public function autocomplete(Request $request) {
        $result = PartsItem::with([
            'partsInfo' => function($query) {
                $query->select('code', 'description', 'specs', 'model', 'id');
            }
        ])->where(function($query) use ($request) {
            $query->where('serial_number', 'like', "%$request->keyword%")
                // ->orWhere('unique_code', 'like', "%$request->keyword%")
                // ->orWhereHas('partsInfo', function($query) use ($request) {
                //     $query->where('description', 'like', "%$request->keyword%");
                // })
                ->limit(10);
        })->get();

        $result = collect($result)->map(function($item) {
            return [
                'id' => $item->id,
                'code' => $item->partsInfo['code'] . ' - ' . $item->serial_number,
                'description' => $item->partsInfo['description'],
                'model' => $item->partsInfo['model'],
                'serialNumber' => $item->serial_number,
                'display' => $item->partsInfo['code'] . ' - ' . $item->serial_number . '(' . $item->partsInfo['model'] . ' - ' . $item->partsInfo['description'] . ')'
            ];
        });

        return response()->json([
            'data' => $result,
        ], 200);
    }

    public function all(Request $request) {
        $result = PartsItem::with('partsInfo.supplier')->whereHas('partsInfo')->where(function($query) use ($request) {
            $query->where('serial_number', 'like', "%$request->keyword%")
                ->orWhereHas('partsInfo', function($query) use ($request) {
                    $query->where('description', 'like', "%$request->keyword%")
                        ->orWhere('model', 'like', "%$request->keyword%")
                        ->orWhere('code', 'like', "%$request->keyword%");
                });
        });

        if($request->supplierId) {
            $result = $result->whereHas('partsInfo', function($query) use ($request) {
                $query->where('supplier_id', $request->supplierId);
            });
        }

        if($request->itemType) {
            $result = $result->whereHas('partsInfo', function($query) use ($request) {
                $query->where('item_type', $request->itemType);
            });
        }

        return response()->json([
            'result' => $result->paginate(10),
        ], 200);
    }

    public function index($id, Request $request) {
        $partsInfo = PartsInfo::findOrFail($id);
        $partsItems = PartsItem::where('parts_info_id', $id)
            ->where(function($query) use ($request) {
                $query->where('serial_number', 'like', "%$request->keyword%");
            });

        // $result = PartsInfo::with([
        //     'partsItems' => function($query) use ($request) {
        //         $query->where(function($query) use ($request) {
        //             $query->where('unique_code', 'like', "$request->keyword%")
        //                 ->orWhere('serial_number', 'like', "$request->keyword%")
        //                 ->paginate(10);
        //         });
        //     }
        // ])->findOrFail($id);

        return response()->json([
            'result' => [
                'partsItems' => $partsItems->paginate(10),
                'partsInfo' => $partsInfo,
            ]
        ], 200);
    }

    public function show($id) {
        $partsItem = PartsItem::findOrFail($id);

        return response()->json([
            'partsItem' => $partsItem,
        ], 200);
    }

    public function delete($id) {
        $partsItem = PartsItem::findOrFail($id);
        return DB::transaction(function () use ($partsItem, $id) {
            if($partsItem->delete()) {

                ActivityLog::create([
                    'title' => 'Delete machine',
                    'remarks' => auth()->user()->fullname . " deleted " . $partsItem->serial_number . "",
                    'tag' => 'delete-machine',
                    'user_id' => auth('api')->id(),
                    'parts_item_id' => $partsItem->id,
                ]);

                return response()->json([
                    'message' => 'Item deleted successfully',
                    'id' => $id
                ], 200);
            }
        });
    }

    public function store($partsInfoId, Request $request) {
        $partsInfo = PartsInfo::findOrFail($partsInfoId);

        $rules = [
            'serialNumber' => 'required|unique:parts_items,serial_number',
            'locationTo' => 'required',
            'specificLocation' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $partsInfo) {
                // if($partsInfo == null) {
                //     $partsInfo = PartsInfo::create([
                //         'code' => $request->commonItemNumber,
                //         'description' => $request->description,
                //         'specs' => $request->specs,
                //         'model' => $request->model,
                //         'supplier_id' => $request->supplierId,
                //         'item_type' => $request->itemType,
                //     ]);
                // }


                // $incomingRepoort = IncomingReport::where('tracking_number', $request->trackingNumber)->first();

                // if($incomingRepoort == null && $request->trackingNumber != null) {
                //     $incomingRepoort = IncomingReport::create([
                //         'date' => $request->date,
                //         'truck_number' => $request->truckNumber,
                //         'dr_number' => $request->DRNumber,
                //         'rr_number' => $request->RRNumber,
                //         'bl_number' => $request->BLNumber,
                //         'pi_number' => $request->PINumber,
                //         'bill_number' => $request->billNumber,
                //         'user_id' => auth('api')->id(),
                //         'tracking_number' => $request->trackingNumber,
                //     ]);
                // }

                $partsItem = PartsItem::create([
                    'parts_info_id' => $partsInfo->id,
                    'serial_number' => $request->serialNumber,
                    'current_location' => $request->locationTo,
                    'specific_location' => $request->specificLocation,
                    // 'incoming_report_id' => $incomingRepoort->id,
                    'specific_location' => $request->specificLocation,
                ]);

                ActivityLog::create([
                    'title' => 'Added ' . $partsInfo->item_type . ' in inventory',
                    'remarks' => auth('api')->user()->fullname . " added " . $partsItem->serial_number . " to inventory",
                    'tag' => 'add-machine',
                    'user_id' => auth('api')->id(),
                    'parts_item_id' => $partsItem->id,
                ]);

                return [
                    'partsInfo' => $partsInfo,
                    'partsItem' => $partsItem,
                ];
            });
        }
    }
}
