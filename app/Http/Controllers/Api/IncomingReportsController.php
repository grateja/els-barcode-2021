<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\IncomingReport;
use App\PartsItem;
use Illuminate\Support\Facades\DB;
use App\PartsInfo;
use App\Origin;
use Carbon\Carbon;
use App\PartsItemLog;
use App\IncomingMachineReport;
use App\IncomingSparepartReport;
use App\IncomingMachineItem;
use App\IncomingSparepartItem;
use App\ActivityLog;

class IncomingReportsController extends Controller
{
    public function incomingMachines(Request $request) {
        $result = IncomingMachineReport::withCount([
            'incomingMachineItems as total' => function($query) {
                $query->whereHas('partsItem.partsInfo');
            }
        ])->where(function($query) use ($request) {
            $query->where('po_number', 'like', "%$request->keyword%")
                ->orWhere('rr_number', 'like', "%$request->keyword%")
                ->orWhere('dr_number', 'like', "%$request->keyword%")
                ->orWhere('pi_number', 'like', "%$request->keyword%");
        });

        if($request->dateReceived) {
            $result = $result->whereDate('date_received', $request->dateReceived);
        }

        return response()->json([
            'result' => $result->paginate(10)
        ], 200);
    }

    public function incomingSpareparts(Request $request) {
        $result = IncomingSparepartReport::withCount([
            'incomingSparepartItems as total' => function($query) {
                $query->whereHas('partsItem.partsInfo');
            }
        ])->where(function($query) use ($request) {
            $query->where('order_number', 'like', "%$request->keyword%")
                ->orWhere('rr_number', 'like', "%$request->keyword%")
                ->orWhere('pi_number', 'like', "%$request->keyword%");
        });

        if($request->dateReceived) {
            $result = $result->whereDate('date_received', $request->dateReceived);
        }

        return response()->json([
            'result' => $result->paginate(10)
        ], 200);
    }

    public function index(Request $request) {
        $result = IncomingReport::where(function($query) use ($request) {
            $query->where('tracking_number', 'like', "$request->keyword%");
        })->orderBy('date', 'desc');

        return response()->json([
            'result' => $result->paginate(10)
        ], 200);
    }

    public function show($itemType, $id) {
        if($itemType == 'machine') {
            $incomingReport = IncomingMachineReport::findOrFail($id);
        } else {
            $incomingReport = IncomingSparepartReport::findOrFail($id);
        }
        return response()->json([
            'incomingReport' => $incomingReport,
        ], 200);
    }

    public function viewItems($id, Request $request) {
        // $result = IncomingReport::with(['partsItems.partsInfo','partsItems' => function($query) use ($request) {
        //     $query->where(function($query) use ($request) {
        //         $query->where('unique_code', 'like', "$request->keyword%")
        //             ->orWhereHas('partsInfo', function($query) use ($request){
        //                 $query->where('description', 'like', "%$request->keyword%");
        //             });
        //     })->paginate(10);
        // }])->findOrFail($id);

        $result = PartsItem::with('partsInfo')->where(function($query) use ($request) {
            $query->where('unique_code', 'like', "$request->keyword%")
                ->orWhereHas('partsInfo', function($query) use ($request){
                    $query->where('description', 'like', "%$request->keyword%");
                });
        })->where('incoming_report_id', $id)->paginate(10);

        return response()->json([
            'result' => $result
        ], 200);
    }

    public function update($itemType, $id, Request $request) {
        if($itemType == 'machine') {
            $incomingReport = IncomingMachineReport::findOrFail($id);
        } else {
            $incomingReport = IncomingSparepartReport::findOrFail($id);
        }



        // $incomingReport = IncomingReport::findOrFail($id);

        // $rules = [
        //     'trackingNumber' => 'required',
        // ];

        // if($request->validate($rules)) {
        //     $incomingReport->update([
        //         'date' => $request->date,
        //         'tracking_number' => $request->trackingNumber,
        //         'dr_number' => $request->DRNumber,
        //         'rr_number' => $request->RRNumber,
        //         'bl_number' => $request->BLNumber,
        //         'pi_number' => $request->PINumber,
        //         'bill_number' => $request->billNumber,
        //         'truck_number' => $request->truckNumber,
        //     ]);

        //     return response()->json([
        //         'incomingReport' => $incomingReport,
        //     ], 200);
        // }
    }

    // public function store($itemType, Request $request) {
    //     $rules = [
    //         'PONumber' => 'required',
    //         'dateReceived' => 'required|date',
    //     ];

    //     if($request->validate($rules)) {
    //         return DB::transaction(function () use ($request, $itemType) {

    //             if($itemType == 'machine') {
    //                 $incomingReport = IncomingMachineReport::create([
    //                     'date_received' => $request->dateReceived,
    //                     'po_number' => $request->PONumber,
    //                     'rr_number' => $request->RRNumber,
    //                     'dr_number' => $request->DRNumber,
    //                     'pi_number' => $request->PINumber,
    //                     'billing_number' => $request->billNumber,
    //                     'truck_number' => $request->truckNumber,
    //                 ]);
    //             }

    //             return response()->json([
    //                 'incomingReport' => $incomingReport,
    //             ], 200);
    //         });
    //     }
    // }

    public function addItem($incomingId, $itemType, Request $request) {
        $rules = [
            'serialNumber' => 'required',
        ];

        $partsItem = PartsItem::where('serial_number', $request->serialNumber)->first();

        if($partsItem == null) {
            $rules = array_merge($rules, [
                'supplier' => 'required',
                'commonCode' => 'required',
                'description' => 'required',
                'locationFrom' => 'required',
                'locationTo' => 'required',
            ]);

            if($itemType == 'machine') {
                $rules = array_merge($rules, [
                    'model' => 'required',
                ]);
            }
        } else if($partsItem->status != 'in_inventory') {
            return response()->json([
                'errors' => [
                    'serialNumber' => ['Item is not available in the inventory.']
                ]
            ], 422);
        }

        if($itemType == 'machine') {
            $incomingReport = IncomingMachineReport::findOrFail($incomingId);
        } else {
            $incomingReport = IncomingSparepartReport::findOrFail($incomingId);
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($partsItem, $incomingReport, $request, $itemType) {
                $partsInfo = PartsInfo::where('code', $request->commonCode)->first();

                if($partsInfo == null && $partsItem == null) {
                    $partsInfo = PartsInfo::create([
                        'code' => $request->commonCode,
                        'description' => $request->description,
                        'specs' => $request->specs,
                        'model' => $request->model,
                        'item_type' => $itemType,
                    ]);
                }

                if($partsItem == null) {
                    $partsItem = PartsItem::create([
                        'parts_info_id' => $partsInfo->id,
                        'serial_number' => $request->serialNumber,
                        'status' => 'in_inventory',
                        'current_location' => $request->locationTo,
                        'specific_location' => $request->specificLocation,
                    ]);
                }

                if($itemType == 'machine') {
                    $incomingItem = IncomingMachineItem::create([
                        'incoming_machine_report_id' => $incomingReport->id,
                        'parts_item_id' => $partsItem->id,
                    ]);
                } else {
                    $incomingItem = IncomingSparepartItem::create([
                        'incoming_sparepart_report_id' => $incomingReport->id,
                        'parts_item_id' => $partsItem->id,
                    ]);
                }

                ActivityLog::create([
                    'title' => 'Added ' . $itemType . ' in inventory',
                    'remarks' => auth('api')->user()->fullname . " added " . $partsItem->serial_number . " to inventory",
                    'tag' => 'add-' . $itemType,
                    'user_id' => auth('api')->id(),
                    'parts_item_id' => $partsItem->id,
                ]);

                return response()->json([
                    'incomingItem' => $incomingItem->fresh('partsItem.partsInfo')
                ], 200);

            });
        }
    }

    public function _addItem($id, Request $request) {
        $incomingReport = IncomingReport::findOrFail($id);

        $rules = [
            'partNumber' => 'required',
            'uniqueCode' => 'required',
            'locationFrom' => 'required',
            'locationTo' => 'required',
            'specificLocation' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $incomingReport) {

                $partsInfo = PartsInfo::where('code', $request->partNumber)->first();
                if($partsInfo == null) {
                    if($request->description == null) {
                        return response()->json([
                            'errors' => [
                                'description' => ['Part info not found. Description is required when creating a new one.']
                            ]
                        ], 422);
                    }
                    $partsInfo = PartsInfo::create([
                        'code' => $request->partNumber,
                        'description' => $request->description,
                        'specs' => $request->specs,
                        'model' => $request->model,
                        'item_type' => $request->itemType,
                        'supplier_id' => $request->supplierId
                    ]);
                }

                $partsItem = PartsItem::create([
                    'unique_code' => $request->uniqueCode,
                    'parts_info_id' => $partsInfo->id,
                    'serial_number' => $request->serialNumber,
                    'incoming_report_id' => $incomingReport->id,
                    'specific_location' => $request->specificLocation,
                ]);

                $origin = Origin::create([
                    'parts_item_id' => $partsItem->id,
                    'location_id_from' => $request->locationFrom['id'],
                    'location_id_to' => $request->locationTo['id'],
                    'user_id' => auth('api')->id(),
                    'date_moved' => Carbon::now(),
                ]);

                PartsItemLog::insert([
                    'parts_item_id' => $partsItem->id,
                    'title' => 'Added a new item (' . $partsItem->unique_code . ')',
                    'user_id' => auth('api')->id(),
                ]);

                return response()->json([
                    'partsItem' => $partsItem->fresh('partsInfo')
                ], 200);
            });
        }
    }

    public function removeItem($id, $itemType) {
        if($itemType == 'machine') {
            $incomingReportItem = IncomingMachineItem::findOrFail($id);
        } else {
            $incomingReportItem = IncomingSparepartItem::findOrFail($id);
        }

        return DB::transaction(function () use ($incomingReportItem, $itemType) {
            if($incomingReportItem->delete()) {

                ActivityLog::create([
                    'title' => 'Removed ' . $itemType . ' from inventory',
                    'remarks' => auth('api')->user()->fullname . " removed " . $incomingReportItem->partsItem->serial_number . " from inventory",
                    'tag' => 'add-' . $itemType,
                    'user_id' => auth('api')->id(),
                    'parts_item_id' => $incomingReportItem->parts_item_id,
                ]);

                return response()->json([
                    'message' => 'Item deleted successfuly'
                ], 200);
            }
        });

    }

    public function delete($id, $itemType) {
        if($itemType == 'machine') {
            $incomingReport = IncomingMachineReport::findOrFail($id);
        } else {
            $incomingReport = IncomingSparepartReport::findOrFail($id);
        }

        return DB::transaction(function () use ($incomingReport) {
            if($incomingReport->delete()) {
                return response()->json([
                    'message' => 'Report deleted successfuly',
                ], 200);
            }
        });

        // $partsInfo = IncomingReport::findOrFail($id);

        // return DB::transaction(function () use ($id, $partsInfo) {
        //     if($partsInfo->delete()) {
        //         return response()->json([
        //             'message' => 'Deleted successfully.',
        //             'id' => $id
        //         ], 200);
        //     }
        // });
    }
}
