<?php

namespace App\Http\Controllers\Api;

use App\ActivityLog;
use App\FinishedGood;
use App\FinishedGoodItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SeriesConflict;
use Illuminate\Support\Facades\DB;

class FinishedGoodItemsController extends Controller
{
    public function index(Request $request, $model = null) {
        $result = DB::table('finished_good_items')
            ->join('finished_goods', 'finished_goods.id', '=', 'finished_good_items.finished_good_id')
            ->selectRaw('`finished_good_items`.`id` AS serial_number, `finished_goods`.`id` AS model, description, specs, warehouse, current_location, supplier, finished_good_items.created_at as created_at')
            ->where(function($query) use ($request){
                $query->where(DB::raw('finished_good_items.id'), 'like', "%$request->keyword%")
                    ->orWhere(DB::raw('finished_goods.id'), 'like', "%$request->keyword%");
            })->whereNull('finished_good_items.deleted_at');

        if($model) {
            $result = $result->where('finished_good_id', $model);
        }

        if($request->date) {
            $result = $result->whereDate(DB::raw('finished_good_items.created_at'), $request->date);
        }

        return response()->json([
            'result' => $result->paginate(10),
        ]);
    }

    public function create(Request $request) {
        if($err = SeriesConflict::check($request, 'serialNumber')) {
            return $err;
        }

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

                return response()->json([
                    'finishedGood' => [
                        'serial_number' => $finishedGoodItem->id,
                        'model' => $finishedGoodItem->model,
                        'description' => $finishedGoodItem->description,
                        'specs' => $finishedGoodItem->specs,
                        'supplier' => $finishedGoodItem->supplier,
                        'warehouse' => $finishedGoodItem->warehouse,
                        'current_location' => $finishedGoodItem->current_location,
                    ]
                ]);
            }
        });
    }

    public function update(Request $request, $serialNumber) {
        $rules = [
            'serialNumber' => 'required',
            'model' => 'required',
            'description' => 'required',
            'supplier' => 'required',
        ];

        $finishedGoodItem = FinishedGoodItem::findOrFail($serialNumber);

        if($finishedGoodItem->id != $request->serialNumber) {
            if($err = SeriesConflict::check($request, 'serialNumber')) {
                return $err;
            }
            $rules['serialNumber'] = 'required|unique:finished_good_items,id';
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $finishedGoodItem) {
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

                return response()->json([
                    'finishedGood' => [
                        'serial_number' => $finishedGoodItem->id,
                        'model' => $finishedGoodItem->model,
                        'description' => $finishedGoodItem->description,
                        'specs' => $finishedGoodItem->specs,
                        'supplier' => $finishedGoodItem->supplier,
                        'warehouse' => $finishedGoodItem->warehouse,
                        'current_location' => $finishedGoodItem->current_location,
                    ]
                ]);
            });
        }
    }

    public function insertSerial(Request $request, $model) {
        $rules = [
            'serialNumber' => 'required|unique:finished_good_items,id',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $model) {
                $finishedGoodItem = FinishedGoodItem::create([
                    'finished_good_id' => $model,
                    'id' => $request->serialNumber,
                    'warehouse' => $request->warehouse,
                    'current_location' => $request->currentLocation,
                ]);

                return response()->json([
                    'finishedGood' => $finishedGoodItem,
                ]);
            });
        }
    }

    public function updateSerial(Request $request, $serialNumber) {
        $finishedGoodItem = FinishedGoodItem::findOrFail($serialNumber);

        if($finishedGoodItem->id != $request->serialNumber) {
            $rules = [
                'serialNumber' => 'required|unique:finished_good_items,id',
            ];
        } else {
            $rules = [
                'serialNumber' => 'required',
            ];
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $finishedGoodItem) {
                $finishedGoodItem->update([
                    'id' => $request->serialNumber,
                    'warehouse' => $request->warehouse,
                    'current_location' => $request->currentLocation,
                ]);

                return response()->json([
                    'finishedGood' => $finishedGoodItem,
                ]);
            });
        }
    }

    public function delete($serialNumber) {
        $finishedGoodItem = FinishedGoodItem::findOrFail($serialNumber);

        return DB::transaction(function () use ($finishedGoodItem) {
            if($finishedGoodItem->delete()) {
                return response()->json([
                    'id' => $finishedGoodItem->id,
                ]);
            }
        });
    }
}
