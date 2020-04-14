<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\OutgoingMachineReport;
use App\PartsItem;
use App\OutgoingSparepartReport;
use Illuminate\Support\Facades\DB;
use App\PartsInfo;
use App\IncomingMachineItem;
use App\IncomingSparepartItem;
use App\ActivityLog;
use App\OutgoingMachineItem;
use App\OutgoingSparepartItem;

class OutgoingReportsController extends Controller
{
    public function outgoingMachines(Request $request) {
        $result = OutgoingMachineReport::with('subdealer', 'client')->withCount([
            'outgoingMachineItems as total' => function($query) {
                $query->whereHas('partsItem.partsInfo');
            }
        ])->where(function($query) use ($request) {
            // $query->where('po_number', 'like', "%$request->keyword%")
            //     ->orWhere('rr_number', 'like', "%$request->keyword%")
            //     ->orWhere('dr_number', 'like', "%$request->keyword%")
            //     ->orWhere('pi_number', 'like', "%$request->keyword%");
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

    public function outgoingSpareparts(Request $request) {
        $result = OutgoingSparepartReport::with('subdealer', 'client')->withCount([
            'outgoingSparepartItems as total' => function($query) {
                $query->whereHas('partsItem.partsInfo');
            }
        ])->where(function($query) use ($request) {
            $query->where('quotation_number', 'like', "%$request->keyword%")
                ->orWhere('sales_invoice', 'like', "%$request->keyword%")
                ->orWhere('warranty_number', 'like', "%$request->keyword%")
                ->orWhere('dr_number', 'like', "%$request->keyword%");
        });

        return response()->json([
            'result' => $result->paginate(10)
        ], 200);
    }

    public function show($itemType, $id) {
        if($itemType == 'machine') {
            $outgoingReport = OutgoingMachineReport::with('client', 'subDealer')->findOrFail($id);
        } else {
            $outgoingReport = OutgoingSparepartReport::with('client', 'subDealer')->findOrFail($id);
        }
        return response()->json([
            'outgoingReport' => $outgoingReport,
        ], 200);
    }

    public function addItem($incomingId, $itemType, Request $request) {
        $rules = [
            'serialNumber' => 'required',
        ];

        $partsItem = PartsItem::where('serial_number', $request->serialNumber)->first();

        if($partsItem == null) {
            return response()->json([
                'errors' => [
                    'serialNumber' => ['Item is not available in the inventory.']
                ]
            ], 422);
            // $rules = array_merge($rules, [
            //     'supplier' => 'required',
            //     'commonCode' => 'required',
            //     'description' => 'required',
            //     'locationFrom' => 'required',
            //     'locationTo' => 'required',
            // ]);

            // if($itemType == 'machine') {
            //     $rules = array_merge($rules, [
            //         'model' => 'required',
            //     ]);
            // }
        } else if($partsItem->status != 'in_inventory') {
            // return response()->json([
            //     'errors' => [
            //         'serialNumber' => ['Item is not available in the inventory.']
            //     ]
            // ], 422);
        }

        if($itemType == 'machine') {
            $outgoingReport = OutgoingMachineReport::with('client')->findOrFail($incomingId);
        } else {
            $outgoingReport = OutgoingSparepartReport::with('client')->findOrFail($incomingId);
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($partsItem, $outgoingReport, $request, $itemType) {
                // $partsInfo = PartsInfo::where('code', $request->commonCode)->first();

                // if($partsInfo == null && $partsItem == null) {
                //     $partsInfo = PartsInfo::create([
                //         'code' => $request->commonCode,
                //         'description' => $request->description,
                //         'specs' => $request->specs,
                //         'model' => $request->model,
                //         'item_type' => $itemType,
                //     ]);
                // }

                // if($partsItem == null) {
                //     $partsItem = PartsItem::create([
                //         'parts_info_id' => $partsInfo->id,
                //         'serial_number' => $request->serialNumber,
                //         'status' => 'in_inventory',
                //         'current_location' => $request->locationTo,
                //         'specific_location' => $request->specificLocation,
                //     ]);
                // }

                if($itemType == 'machine') {
                    $outgoingItem = OutgoingMachineItem::create([
                        'outgoing_machine_report_id' => $outgoingReport->id,
                        'parts_item_id' => $partsItem->id,
                    ]);
                } else {
                    $outgoingItem = OutgoingSparepartItem::create([
                        'outgoing_sparepart_report_id' => $outgoingReport->id,
                        'parts_item_id' => $partsItem->id,
                    ]);
                }

                if($outgoingReport->client != null) {
                    $partsItem->update([
                        'status' => 'issued_to_client',
                        'client_id' => $outgoingReport->client->id,
                        'current_location' => $outgoingReport->client->name,
                        'specific_location' => $outgoingReport->client->address,
                    ]);

                    ActivityLog::create([
                        'title' => 'Issued ' . $itemType . ' to client',
                        'remarks' => auth('api')->user()->fullname . " issued " . $partsItem->serial_number . " to client" . $outgoingReport->client->name,
                        'tag' => 'add-' . $itemType,
                        'user_id' => auth('api')->id(),
                        'parts_item_id' => $partsItem->id,
                    ]);
                } else if($outgoingReport->subDealer != null) {
                    $partsItem->update([
                        'status' => 'issued_to_subdealer',
                        'client_id' => $outgoingReport->client_id,
                        // 'current_location' => $outgoingReport->client['name'],
                        // 'specific_location' => $outgoingReport->client->address,
                    ]);

                    ActivityLog::create([
                        'title' => 'Issued ' . $itemType . ' to client',
                        'remarks' => auth('api')->user()->fullname . " issued " . $partsItem->serial_number . " to client" . $outgoingReport->client['name'],
                        'tag' => 'add-' . $itemType,
                        'user_id' => auth('api')->id(),
                        'parts_item_id' => $partsItem->id,
                    ]);
                } else {
                    DB::rollback();

                    return response()->json([
                        'errors' => [
                            'serialNumber' => ['Client or Sub dealer is empty. Please go back to report preferences.']
                        ]
                    ], 422);
                }


                return response()->json([
                    'outgoingItem' => $outgoingItem->fresh('partsItem.partsInfo')
                ], 200);

            });
        }
    }

    public function removeItem($id, $itemType) {
        if($itemType == 'machine') {
            $outgoingItem = OutgoingMachineItem::findOrFail($id);
        } else {
            $outgoingItem = OutgoingSparepartItem::findOrFail($id);
        }

        return DB::transaction(function () use ($outgoingItem, $itemType) {
            if($outgoingItem->delete()) {

                ActivityLog::create([
                    'title' => 'Removed ' . $itemType . ' from inventory',
                    'remarks' => auth('api')->user()->fullname . " removed " . $outgoingItem->partsItem->serial_number . " from inventory",
                    'tag' => 'add-' . $itemType,
                    'user_id' => auth('api')->id(),
                    'parts_item_id' => $outgoingItem->parts_item_id,
                ]);

                return response()->json([
                    'message' => 'Item deleted successfuly'
                ], 200);
            }
        });
    }

    public function delete($id, $itemType) {
        if($itemType == 'machine') {
            $outgoingItem = OutgoingMachineReport::findOrFail($id);
        } else {
            $outgoingItem = OutgoingSparepartReport::findOrFail($id);
        }

        if($outgoingItem->delete()) {
            return response()->json([
                'message' => 'Outgoing report deleted successfuly'
            ], 200);
        }
    }
}
