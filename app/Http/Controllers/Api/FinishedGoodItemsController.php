<?php

namespace App\Http\Controllers\Api;

use App\ActivityLog;
use App\FinishedGood;
use App\FinishedGoodItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FinishedGoodItemsController extends Controller
{
    public function index(Request $request) {
        $result = DB::table('finished_good_items')
            ->join('finished_goods', 'finished_goods.id', '=', 'finished_good_items.finished_good_id')
            ->selectRaw('`finished_good_items`.`id` AS serial_number, `finished_goods`.`id` AS model, description, specs, warehouse, current_location, supplier')
            ->where(function($query) use ($request){
                $query->where(DB::raw('finished_good_items.id'), 'like', "%$request->keyword%");
            });

        return response()->json([
            'result' => $result->paginate(10),
        ]);
    }

    public function create(Request $request) {
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
}
