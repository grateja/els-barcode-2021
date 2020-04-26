<?php

namespace App\Http\Controllers\Api;

use App\FinishedGood;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FinishedGoodsController extends Controller
{
    public function autocomplete(Request $request) {
        $result = FinishedGood::where('id', 'like', "%$request->keyword%")
            ->orWhere('description', 'like', "%$request->keyword%")
            ->select('*', DB::raw('`id` as model, CONCAT(`id`, " - ", `description`, IF(`specs` <> "", CONCAT(" - (", `specs`, ")"), "")) as display, supplier'))
            ->limit(10)
            ->get();

        return response()->json([
            'data' => $result,
        ], 200);
    }

    public function index(Request $request) {
        $result = FinishedGood::where(function($query) use ($request) {
            $query->where('id', 'like', "%$request->keyword%")
                ->orWhere('description', 'like', "%$request->keyword%")
                ->orWhere('specs', 'like', "%$request->keyword%");
        });

        if($request->date) {
            $result = $result->whereDate('created_at', $request->date);
        }

        if($orderBy = FinishedGood::canOrderBy($request->sortBy)) {
            $result = $result->orderBy($orderBy, $request->orderBy);
        }

        return response()->json([
            'result' => $result->paginate(10),
        ]);
    }

    public function create(Request $request) {
        $rules = [
            'model' => 'required|unique:finished_goods,id',
            'description' => 'required',
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

                return response()->json([
                    'finishedGood' => $finishedGood,
                ]);
            });
        }
    }

    public function update(Request $request, $id) {
        $rules = [
            'model' => 'required',
            'description' => 'required',
            'supplier' => 'required',
        ];

        $finishedGood = FinishedGood::findOrFail($id);

        if($request->model != $finishedGood->id) {
            $rules['model'] = 'required|unique:finished_goods,id';
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $finishedGood) {
                $finishedGood->update([
                    'id' => $request->model,
                    'description' => $request->description,
                    'specs' => $request->specs,
                    'supplier' => $request->supplier,
                ]);

                return response()->json([
                    'finishedGood' => $finishedGood,
                ]);
            });
        }
    }

    public function delete($id) {
        $finishedGood = FinishedGood::findOrFail($id);
        if($finishedGood->delete()) {
            return response()->json([
                'id' => $id
            ]);
        }
    }
}
