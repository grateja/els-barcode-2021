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
    public function show($model) {
        $finishedGood = FinishedGood::withTrashed()
            ->withCount(['finishedGoodItems as total_items' => function($query) {

            }])
            ->find($model);

        if($finishedGood == null) {
            $data = null;
        } else {
            $data = [
                'model' => $finishedGood ? $finishedGood->id : null,
                'description' => $finishedGood ? $finishedGood->description : null,
                'specs' => $finishedGood ? $finishedGood->specs : null,
                'supplier' => $finishedGood ? $finishedGood->supplier : null,
                'deletedAt' => $finishedGood->deleted_at,
                'totalItems' => $finishedGood->total_items,
            ];
        }

        return view('finished-goods.profiles.view-item', [
            'data' => $data,
            'model' => $model,
        ]);
    }

    public function addProfile($model = null, $serialNumber = null) {
        $finishedGood = FinishedGood::find($model);

        return view('finished-goods.profiles.create', [
            'model' => $finishedGood ? $finishedGood->id : $model,
            'description' => $finishedGood ? $finishedGood->description : null,
            'specs' => $finishedGood ? $finishedGood->specs : null,
            'supplier' => $finishedGood ? $finishedGood->supplier : null,
            'action' => $finishedGood == null ? 'insert' : $model . '/update/' . $serialNumber,
            'serialNumber' => $serialNumber,
        ]);
    }

    public function insert(Request $request) {
        $rules = [
            'model' => 'required|unique:finished_goods,id',
            'description' => 'required',
            'specs' => 'required',
            'supplier' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request) {
                $finishedGood = FinishedGood::create([
                    'id' => $request->model,
                    'description' => $request->description,
                    'specs' => $request->specs,
                    'supplier' => $request->supplier,
                ]);

                return redirect(route('scan.any', ['code' => $finishedGood->id]));
            });
        }
    }

    public function update(Request $request, $model, $serialNumber = null) {
        $rules = [
            'model' => 'required',
            'description' => 'required',
            'specs' => 'required',
            'supplier' => 'required',
        ];

        $finishedGood = FinishedGood::findOrFail($model);

        if($finishedGood->id != $request->model) {
            $rules['model'] = 'required|unique:finished_goods,id';
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $finishedGood, $serialNumber) {
                $finishedGood->update([
                    'id' => $request->model,
                    'description' => $request->description,
                    'specs' => $request->specs,
                    'supplier' => $request->supplier,
                ]);

                return redirect(route('scan.any', ['code' => $serialNumber ? $serialNumber : $finishedGood->id]));
            });
        }
    }
}
