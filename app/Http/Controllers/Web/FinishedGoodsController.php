<?php

namespace App\Http\Controllers\Web;

use App\ActivityLog;
use App\FinishedGood;
use App\FinishedGoodItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FinishedGoodsController extends Controller
{
    public function deleteItem($code) {
        $finishedGood = FinishedGoodItem::with('finishedGood')->where('serial_number', $code)->first();
        return view('finished-goods.delete-item', [
            'id' => $finishedGood->id,
            'code' => $code,
        ]);
    }

    private function delete($permanent, $id) {
        return DB::transaction(function () use ($id, $permanent) {
            $finishedGoodItem = FinishedGoodItem::findOrFail($id);
            $code = $finishedGoodItem->serial_number;

            if($permanent) {
                $finishedGoodItem->forceDelete();
            } else {
                $finishedGoodItem->delete();
            }

            ActivityLog::create([
                'finished_good_item_id' => $finishedGoodItem->id,
                'user_id' => auth()->id(),
                'action' => 'DELETE',
                'remarks' => 'deleted by '. auth()->user()->name . ' - ' . $code,
            ]);

            return redirect(route('scan.finished-goods', ['code' => $code]));
        });
    }

    public function deleteItemContinue($id) {
        return $this->delete(false, $id);
    }

    public function permanentlyDeleteItemContinue($id) {
        return $this->delete(true, $id);
    }

    public function addRemarks($code) {
        $finishedGood = FinishedGoodItem::with('finishedGood')->where('serial_number', $code)->first();
        return view('finished-goods.add-remarks', [
            'id' => $finishedGood->id,
            'code' => $code,
        ]);
    }

    public function move($code) {
        $finishedGood = FinishedGoodItem::with('finishedGood')->where('serial_number', $code)->first();
        return view('finished-goods.move', [
            'id' => $finishedGood->id,
            'currentLocation' => $finishedGood->current_location,
            'code' => $code,
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

                return redirect(route('scan.finished-goods', ['code' => $finishedGoodItem->serial_number]));
            });
        }
    }

    public function moveContinue(Request $request, $id) {
        $rules = [
            'locationTo' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $id) {
                $finishedGoodItem = FinishedGoodItem::findOrFail($id);
                $currentLocation = $finishedGoodItem->current_location;

                $finishedGoodItem->update([
                    'current_location' => $request->locationTo,
                ]);

                ActivityLog::create([
                    'finished_good_item_id' => $finishedGoodItem->id,
                    'user_id' => auth()->id(),
                    'action' => 'MOVE',
                    'remarks' => 'moved by '. auth()->user()->name . '. From ' . $currentLocation . ' to ' . $request->locationTo,
                ]);

                return redirect(route('scan.finished-goods', ['code' => $finishedGoodItem->serial_number]));
            });
        }
    }

    public function show($code) {
        $finishedGood = FinishedGoodItem::with('finishedGood', 'subdealer', 'client', 'activityLogs')->where('serial_number', $code)->first();

        if($finishedGood == null) {
            $model = null;
        } else {
            $model = [
                'code' => $code,
                'model' => $finishedGood ? $finishedGood->model : null,
                'description' => $finishedGood ? $finishedGood->description : null,
                'specs' => $finishedGood ? $finishedGood->specs : null,
                'supplier' => $finishedGood ? $finishedGood->supplier : null,
                'serialNumber' => $finishedGood ? $finishedGood->serial_number : $code,
                'warehouse' => $finishedGood ? $finishedGood->warehouse : null,
                'currentLocation' => $finishedGood ? $finishedGood->current_location : null,
                'dateScanned' => $finishedGood->created_at->format('y/M/D - h:i A'),
                'client' => $finishedGood->client,
                'subdealer' => $finishedGood->subdealer,
                'activityLogs' => $finishedGood->activityLogs,
                'incomingReport' => $finishedGood->incomingReport(),
                'outgoingReport' => $finishedGood->outgoingReport(),
            ];
        }

        return view('finished-goods.view-item', [
            'model' => $model,
            'code' => $code,
        ]);
    }

    public function addToInventory($code) {
        $finishedGood = FinishedGoodItem::with('finishedGood')->where('serial_number', $code)->first();

        return view('finished-goods.add-to-inventory', [
            'code' => $code,
            'id' => $finishedGood ? '/' . $finishedGood->id : null,
            'model' => $finishedGood ? $finishedGood->model : null,
            'description' => $finishedGood ? $finishedGood->description : null,
            'specs' => $finishedGood ? $finishedGood->specs : null,
            'supplier' => $finishedGood ? $finishedGood->supplier : null,
            'serialNumber' => $finishedGood ? $finishedGood->serial_number : $code,
            'warehouse' => $finishedGood ? $finishedGood->warehouse : null,
            'currentLocation' => $finishedGood ? $finishedGood->current_location : null,
            'action' => $finishedGood == null ? 'insert' : 'update',
        ]);
    }

    public function update(Request $request, $id) {
        return DB::transaction(function () use ($request, $id) {
            $rules = [
                'serialNumber' => 'required',
                'model' => 'required',
            ];

            $finishedGoodItem = FinishedGoodItem::findOrFail($id);

            if($finishedGoodItem->serial_number != $request->serialNumber) {
                // serial number was edited
                $rules['serialNumber'] = 'required|unique:finished_good_items,serial_number';
            }

            if($request->validate($rules)) {
                $finishedGood = FinishedGood::where('model', $request->model)->first();

                if($finishedGood == null) {
                    $finishedGood = FinishedGood::create([
                        'model' => $request->model,
                        'description' => $request->description,
                        'specs' => $request->specs,
                        'supplier' => $request->supplier,
                    ]);
                } else {
                    if($request->model != null) {
                        $finishedGood->model = $request->model;
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
                    'serial_number' => $request->serialNumber,
                    'warehouse' => $request->warehouse,
                    'current_location' => $request->currentLocation,
                    'status' => 'IN_INVENTORY',
                ]);

                ActivityLog::create([
                    'finished_good_item_id' => $finishedGoodItem->id,
                    'user_id' => auth()->id(),
                    'action' => 'UPDATE',
                    'remarks' => 'updated by '. auth()->user()->name,
                ]);

                return redirect(route('scan.finished-goods', ['code' => $finishedGoodItem->serial_number]));
            }
        });
    }

    public function insert(Request $request) {
        return DB::transaction(function () use ($request) {
            $rules = [
                'serialNumber' => 'required|unique:finished_good_items,serial_number',
                'model' => 'required',
            ];

            if($request->validate($rules)) {
                $finishedGood = FinishedGood::where('model', $request->model)->first();

                if($finishedGood == null) {
                    $finishedGood = FinishedGood::create([
                        'model' => $request->model,
                        'description' => $request->description,
                        'specs' => $request->specs,
                        'supplier' => $request->supplier,
                    ]);
                } else {
                    if($request->model != null) {
                        $finishedGood->model = $request->model;
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
                    'serial_number' => $request->serialNumber,
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

                return redirect(route('scan.finished-goods', ['code' => $finishedGoodItem->serial_number]));
            }
        });
    }
}
