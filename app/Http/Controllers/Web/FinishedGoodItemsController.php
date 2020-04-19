<?php

namespace App\Http\Controllers\Web;

use App\ActivityLog;
use App\FinishedGood;
use App\FinishedGoodItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FinishedGoodItemsController extends Controller
{
    public function show($serialNumber) {
        $finishedGood = FinishedGoodItem::findAll($serialNumber);

        if($finishedGood == null) {
            $model = null;
        } else {
            $model = [
                'model' => $finishedGood ? $finishedGood->model : null,
                'description' => $finishedGood ? $finishedGood->description : null,
                'specs' => $finishedGood ? $finishedGood->specs : null,
                'supplier' => $finishedGood ? $finishedGood->supplier : null,
                'warehouse' => $finishedGood ? $finishedGood->warehouse : null,
                'currentLocation' => $finishedGood ? $finishedGood->current_location : null,
                'dateScanned' => $finishedGood->created_at->format('y/M/D - h:i A'),
                'client' => $finishedGood->client,
                'subdealer' => $finishedGood->subdealer,
                'activityLogs' => $finishedGood->activityLogs,
                'incomingReport' => $finishedGood->incomingReport(),
                'outgoingReport' => $finishedGood->outgoingReport(),
                'deletedAt' => $finishedGood->deleted_at,
            ];
        }

        return view('finished-goods.view-item', [
            'model' => $model,
            'serialNumber' => $serialNumber,
        ]);
    }

    public function addToInventory($serialNumber) {
        $finishedGood = FinishedGoodItem::findAll($serialNumber);

        return view('finished-goods.add-to-inventory', [
            'serialNumber' => $finishedGood ? $finishedGood->id : $serialNumber,
            'model' => $finishedGood ? $finishedGood->model : null,
            'description' => $finishedGood ? $finishedGood->description : null,
            'specs' => $finishedGood ? $finishedGood->specs : null,
            'supplier' => $finishedGood ? $finishedGood->supplier : null,
            'warehouse' => $finishedGood ? $finishedGood->warehouse : null,
            'currentLocation' => $finishedGood ? $finishedGood->current_location : null,
            'action' => $finishedGood == null ? 'insert' : $serialNumber . '/update',
        ]);
    }

    public function insert(Request $request) {
        return DB::transaction(function () use ($request) {
            $rules = [
                'serialNumber' => 'required|unique:finished_good_items,id',
                'model' => 'required',
                'description' => 'required',
                'specs' => 'required',
                'supplier' => 'required',
            ];

            if($request->validate($rules)) {
                $finishedGood = FinishedGood::find($request->model);

                if($finishedGood == null) {
                    $finishedGood = FinishedGood::create([
                        'id' => $request->model,
                        'description' => $request->description,
                        'specs' => $request->specs,
                        'supplier' => $request->supplier,
                    ]);
                } else {
                    if($request->model != null) {
                        $finishedGood->id = $request->model;
                    }
                    if($request->description != null) {
                        $finishedGood->description = $request->description;
                    }
                    if($request->specs != null) {
                        $finishedGood->specs = $request->specs;
                    }
                    if($request->supplier != null) {
                        $finishedGood->supplier = $request->supplier;
                    }
                    $finishedGood->save();
                }

                $finishedGoodItem = FinishedGoodItem::create([
                    'finished_good_id' => $finishedGood->id,
                    'id' => $request->serialNumber,
                    'warehouse' => $request->warehouse,
                    'current_location' => $request->currentLocation,
                    'status' => 'IN_INVENTORY',
                ]);

                ActivityLog::create([
                    'finished_good_item_id' => $finishedGoodItem->id,
                    'user_id' => auth()->id(),
                    'action' => 'INSERT',
                    'remarks' => 'inserted by '. auth()->user()->name,
                ]);

                return redirect(route('scan.finished-goods', ['code' => $finishedGoodItem->id]));
            }
        });
    }

    public function update(Request $request, $serialNumber) {
        return DB::transaction(function () use ($request, $serialNumber) {
            $rules = [
                'serialNumber' => 'required',
                'model' => 'required',
                'description' => 'required',
                'specs' => 'required',
                'supplier' => 'required',
            ];

            $finishedGoodItem = FinishedGoodItem::findOrFail($serialNumber);

            if($finishedGoodItem->id != $request->serialNumber) {
                // serial number was edited
                $rules['serialNumber'] = 'required|unique:finished_good_items,id';
            }


            if($request->validate($rules)) {
                $finishedGood = FinishedGood::find($request->model);

                if($finishedGood == null) {
                    $finishedGood = FinishedGood::create([
                        'id' => $request->model,
                        'description' => $request->description,
                        'specs' => $request->specs,
                        'supplier' => $request->supplier,
                    ]);
                } else {
                    if($request->model != null) {
                        $finishedGood->id = $request->model;
                    }
                    if($request->description != null) {
                        $finishedGood->description = $request->description;
                    }
                    if($request->specs != null) {
                        $finishedGood->specs = $request->specs;
                    }
                    if($request->supplier != null) {
                        $finishedGood->supplier = $request->supplier;
                    }
                    $finishedGood->save();
                }

                $finishedGoodItem->update([
                    'id' => $request->serialNumber,
                    'finished_good_id' => $request->model,
                    'warehouse' => $request->warehouse,
                    'current_location' => $request->currentLocation,
                ]);

                ActivityLog::create([
                    'finished_good_item_id' => $finishedGoodItem->id,
                    'user_id' => auth()->id(),
                    'action' => 'UPDATE',
                    'remarks' => 'updated by '. auth()->user()->name,
                ]);

                return redirect(route('scan.finished-goods', ['code' => $finishedGoodItem->id]));
            }
        });
    }

    public function delete($serialNumber) {
        $finishedGood = FinishedGoodItem::findAll($serialNumber);
        return view('finished-goods.delete-item', [
            'serialNumber' => $serialNumber,
            'force' => $finishedGood->deleted_at != null ? 1 : 0,
        ]);
    }

    public function deleteContinue($serialNumber, $force = false) {
        return DB::transaction(function () use ($serialNumber, $force) {
            $finishedGoodItem = FinishedGoodItem::withTrashed()->findOrFail($serialNumber);

            if($force) {
                $finishedGoodItem->forceDelete();
            } else {
                $finishedGoodItem->delete();
            }

            if(!$force) {
                ActivityLog::create([
                    'finished_good_item_id' => $finishedGoodItem->id,
                    'user_id' => auth()->id(),
                    'action' => 'DELETE',
                    'remarks' => 'deleted by '. auth()->user()->name,
                ]);
            }

            return redirect(route('scan.any', ['code' => $serialNumber]));
        });
    }

    public function restore($serialNumber) {
        $finishedGoodItem = FinishedGoodItem::withTrashed()->findOrFail($serialNumber);

        return view('finished-goods.restore', [
            'code' => $serialNumber,
        ]);
    }

    public function restoreContinue($serialNumber) {
        return DB::transaction(function () use ($serialNumber) {
            $finishedGoodItem = FinishedGoodItem::withTrashed()->findOrFail($serialNumber);
            if($finishedGoodItem->restore()) {
                ActivityLog::create([
                    'finished_good_item_id' => $finishedGoodItem->id,
                    'user_id' => auth()->id(),
                    'action' => 'RESTORE',
                    'remarks' => 'restored by '. auth()->user()->name . '.',
                ]);

                return redirect(route('scan.finished-goods', ['code' => $serialNumber]));
            }
        });
    }

    public function addRemarks($serialNumber) {
        $finishedGood = FinishedGoodItem::withTrashed()->findOrFail($serialNumber);
        return view('finished-goods.add-remarks', [
            'serialNumber' => $finishedGood->id,
        ]);
    }

    public function addRemarksContinue(Request $request, $id) {
        $rules = [
            'remarks' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $id) {
                $finishedGoodItem = FinishedGoodItem::findOrFail($id);

                ActivityLog::create([
                    'finished_good_item_id' => $id,
                    'user_id' => auth()->id(),
                    'action' => 'MOVE',
                    'remarks' => $request->remarks,
                ]);

                return redirect(route('scan.finished-goods', ['code' => $finishedGoodItem->id]));
            });
        }
    }

    public function move($serialNumber) {
        $finishedGood = FinishedGoodItem::withTrashed()->findOrFail($serialNumber);
        return view('finished-goods.move', [
            'currentLocation' => $finishedGood->current_location,
            'currentWarehouse' => $finishedGood->warehouse,
            'serialNumber' => $finishedGood->id,
        ]);
    }

    public function moveContinue(Request $request, $serialNumber) {
        $rules = [
            'locationTo' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $serialNumber) {
                $finishedGoodItem = FinishedGoodItem::findOrFail($serialNumber);
                $currentLocation = $finishedGoodItem->current_location;
                $currentWarehouse = $finishedGoodItem->warehouse;

                $finishedGoodItem->update([
                    'current_location' => $request->locationTo,
                    'warehouse' => $request->warehouse,
                ]);

                ActivityLog::create([
                    'finished_good_item_id' => $finishedGoodItem->id,
                    'user_id' => auth()->id(),
                    'action' => 'MOVE',
                    'remarks' => 'moved by '. auth()->user()->name . '. From ' . $currentLocation . '(' . $currentWarehouse . ')' . ' to ' . $request->locationTo . '(' . $request->warehouse . ')',
                ]);

                return redirect(route('scan.finished-goods', ['code' => $finishedGoodItem->id]));
            });
        }
    }
}
