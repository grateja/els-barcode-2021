<?php

namespace App\Http\Controllers\Web;

use App\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SparePart;
use App\SparePartItem;
use Illuminate\Support\Facades\DB;

class SparePartItemsController extends Controller
{
    public function show($serialNumber) {
        $sparePartItem = SparePartItem::findAll($serialNumber);

        if($sparePartItem == null) {
            $model = null;
        } else {
            $model = [
                'partNumber' => $sparePartItem ? $sparePartItem->partNumber : null,
                'description' => $sparePartItem ? $sparePartItem->description : null,
                'specs' => $sparePartItem ? $sparePartItem->specs : null,
                'supplier' => $sparePartItem ? $sparePartItem->supplier : null,
                'warehouse' => $sparePartItem ? $sparePartItem->warehouse : null,
                'currentLocation' => $sparePartItem ? $sparePartItem->current_location : null,
                'dateScanned' => $sparePartItem->created_at->format('y/M/D - h:i A'),
                'client' => $sparePartItem->client,
                'subdealer' => $sparePartItem->subdealer,
                'activityLogs' => $sparePartItem->activityLogs,
                'incomingReport' => $sparePartItem->incomingReport(),
                'outgoingReport' => $sparePartItem->outgoingReport(),
                'deletedAt' => $sparePartItem->deleted_at,
            ];
        }

        return view('spare-parts.view-item', [
            'model' => $model,
            'serialNumber' => $serialNumber,
        ]);
    }

    public function addToInventory($serialNumber) {
        $sparePartItem = SparePartItem::findAll($serialNumber);

        return view('spare-parts.add-to-inventory', [
            'serialNumber' => $sparePartItem ? $sparePartItem->id : $serialNumber,
            'partNumber' => $sparePartItem ? $sparePartItem->partNumber : null,
            'description' => $sparePartItem ? $sparePartItem->description : null,
            'specs' => $sparePartItem ? $sparePartItem->specs : null,
            'supplier' => $sparePartItem ? $sparePartItem->supplier : null,
            'warehouse' => $sparePartItem ? $sparePartItem->warehouse : null,
            'currentLocation' => $sparePartItem ? $sparePartItem->current_location : null,
            'action' => $sparePartItem == null ? 'insert' : $serialNumber . '/update',
        ]);
    }

    public function insert(Request $request) {
        return DB::transaction(function () use ($request) {
            $rules = [
                'serialNumber' => 'required|unique:finished_good_items,id',
                'partNumber' => 'required',
                'description' => 'required',
                'specs' => 'required',
                'supplier' => 'required',
            ];

            if($request->validate($rules)) {
                $sparePart = SparePart::find($request->partNumber);

                if($sparePart == null) {
                    $sparePart = SparePart::create([
                        'id' => $request->partNumber,
                        'description' => $request->description,
                        'specs' => $request->specs,
                        'supplier' => $request->supplier,
                    ]);
                } else {
                    if($request->partNumber != null) {
                        $sparePart->id = $request->partNumber;
                    }
                    if($request->description != null) {
                        $sparePart->description = $request->description;
                    }
                    if($request->specs != null) {
                        $sparePart->specs = $request->specs;
                    }
                    if($request->supplier != null) {
                        $sparePart->supplier = $request->supplier;
                    }
                    $sparePart->save();
                }

                $sparePartItem = SparePartItem::create([
                    'spare_part_id' => $sparePart->id,
                    'id' => $request->serialNumber,
                    'warehouse' => $request->warehouse,
                    'current_location' => $request->currentLocation,
                    'status' => 'IN_INVENTORY',
                ]);

                ActivityLog::create([
                    'spare_part_item_id' => $sparePartItem->id,
                    'user_id' => auth()->id(),
                    'action' => 'INSERT',
                    'remarks' => 'inserted by '. auth()->user()->name,
                ]);

                return redirect(route('scan.spare-parts', ['code' => $sparePartItem->id]));
            }
        });
    }

    public function update(Request $request, $serialNumber) {
        return DB::transaction(function () use ($request, $serialNumber) {
            $rules = [
                'serialNumber' => 'required',
                'partNumber' => 'required',
                'description' => 'required',
                'specs' => 'required',
                'supplier' => 'required',
            ];

            $sparePartItem = SparePartItem::findOrFail($serialNumber);

            if($sparePartItem->id != $request->serialNumber) {
                // serial number was edited
                $rules['serialNumber'] = 'required|unique:spare_part_items,id';
            }


            if($request->validate($rules)) {
                $sparePart = SparePart::find($request->partNumber);

                if($sparePart == null) {
                    $sparePart = SparePart::create([
                        'id' => $request->partNumber,
                        'description' => $request->description,
                        'specs' => $request->specs,
                        'supplier' => $request->supplier,
                    ]);
                } else {
                    if($request->partNumber != null) {
                        $sparePart->id = $request->partNumber;
                    }
                    if($request->description != null) {
                        $sparePart->description = $request->description;
                    }
                    if($request->specs != null) {
                        $sparePart->specs = $request->specs;
                    }
                    if($request->supplier != null) {
                        $sparePart->supplier = $request->supplier;
                    }
                    $sparePart->save();
                }

                $sparePartItem->update([
                    'id' => $request->serialNumber,
                    'warehouse' => $request->warehouse,
                    'current_location' => $request->currentLocation,
                ]);

                ActivityLog::create([
                    'spare_part_item_id' => $sparePartItem->id,
                    'user_id' => auth()->id(),
                    'action' => 'UPDATE',
                    'remarks' => 'updated by '. auth()->user()->name,
                ]);

                return redirect(route('scan.spare-parts', ['code' => $sparePartItem->id]));
            }
        });
    }

    public function delete($serialNumber) {
        $sparePartItem = SparePartItem::withTrashed()->findOrFail($serialNumber);
        return view('spare-parts.delete-item', [
            'serialNumber' => $serialNumber,
            'force' => $sparePartItem->deleted_at != null ? 1 : 0,
        ]);
    }

    public function deleteContinue($serialNumber, $force = false) {
        return DB::transaction(function () use ($serialNumber, $force) {
            $sparePartItem = SparePartItem::withTrashed()->findOrFail($serialNumber);

            if($force) {
                $sparePartItem->forceDelete();
            } else {
                $sparePartItem->delete();
            }

            if(!$force) {
                ActivityLog::create([
                    'spare_part_item_id' => $sparePartItem->id,
                    'user_id' => auth()->id(),
                    'action' => 'DELETE',
                    'remarks' => 'deleted by '. auth()->user()->name,
                ]);
            }

            return redirect(route('scan.spare-parts', ['code' => $serialNumber]));
        });
    }

    public function restore($serialNumber) {
        $sparePartItem = SparePartItem::withTrashed()->findOrFail($serialNumber);

        return view('spare-parts.restore', [
            'code' => $serialNumber,
        ]);
    }

    public function restoreContinue($serialNumber) {
        return DB::transaction(function () use ($serialNumber) {
            $sparePartItem = SparePartItem::withTrashed()->findOrFail($serialNumber);
            if($sparePartItem->restore()) {
                ActivityLog::create([
                    'spare_part_item_id' => $sparePartItem->id,
                    'user_id' => auth()->id(),
                    'action' => 'RESTORE',
                    'remarks' => 'restored by '. auth()->user()->name . '.',
                ]);

                return redirect(route('scan.spare-parts', ['code' => $serialNumber]));
            }
        });
    }

    public function addRemarks($serialNumber) {
        $sparePartItem = SparePartItem::withTrashed()->findOrFail($serialNumber);
        return view('spare-parts.add-remarks', [
            'serialNumber' => $sparePartItem->id,
        ]);
    }

    public function addRemarksContinue(Request $request, $id) {
        $rules = [
            'remarks' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $id) {
                $sparePartItem = SparePartItem::findOrFail($id);

                ActivityLog::create([
                    'spare_part_item_id' => $id,
                    'user_id' => auth()->id(),
                    'action' => 'MOVE',
                    'remarks' => $request->remarks,
                ]);

                return redirect(route('scan.spare-parts', ['code' => $sparePartItem->id]));
            });
        }
    }

    public function move($serialNumber) {
        $sparePartItem = SparePartItem::withTrashed()->findOrFail($serialNumber);
        return view('spare-parts.move', [
            'currentLocation' => $sparePartItem->current_location,
            'currentWarehouse' => $sparePartItem->warehouse,
            'serialNumber' => $sparePartItem->id,
        ]);
    }

    public function moveContinue(Request $request, $serialNumber) {
        $rules = [
            'locationTo' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $serialNumber) {
                $sparePartItem = SparePartItem::findOrFail($serialNumber);
                $currentLocation = $sparePartItem->current_location;
                $currentWarehouse = $sparePartItem->warehouse;

                $sparePartItem->update([
                    'current_location' => $request->locationTo,
                    'warehouse' => $request->warehouse,
                ]);

                ActivityLog::create([
                    'spare_part_item_id' => $sparePartItem->id,
                    'user_id' => auth()->id(),
                    'action' => 'MOVE',
                    'remarks' => 'moved by '. auth()->user()->name . '. From ' . $currentLocation . '(' . $currentWarehouse . ')' . ' to ' . $request->locationTo . '(' . $request->warehouse . ')',
                ]);

                return redirect(route('scan.spare-parts', ['code' => $sparePartItem->id]));
            });
        }
    }
}
