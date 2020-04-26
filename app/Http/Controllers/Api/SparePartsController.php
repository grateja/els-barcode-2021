<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SparePart;
use Illuminate\Support\Facades\DB;

class SparePartsController extends Controller
{
    public function autocomplete(Request $request) {
        $result = SparePart::where('id', 'like', "%$request->keyword%")
            ->orWhere('description', 'like', "%$request->keyword%")
            ->select('*', DB::raw('`id` as partNumber, CONCAT(`id`, " - ", `description`, IF(`specs` <> "", CONCAT(" - (", `specs`, ")"), "")) as display, supplier'))
            ->limit(10)
            ->get();

        return response()->json([
            'data' => $result,
        ], 200);
    }

    public function index(Request $request) {
        $result = SparePart::withCount('sparePartItems')->where(function($query) use ($request) {
            $query->where('id', 'like', "%$request->keyword%")
                ->orWhere('description', 'like', "%$request->keyword%")
                ->orWhere('specs', 'like', "%$request->keyword%");
        });

        if($request->date) {
            $result = $result->whereDate('created_at', $request->date);
        }

        if($orderBy = SparePart::canOrderBy($request->sortBy)) {
            $result = $result->orderBy($orderBy, $request->orderBy);
        }

        return response()->json([
            'result' => $result->paginate(10),
        ]);
    }

    public function create(Request $request) {
        $rules = [
            'partNumber' => 'required|unique:spare_parts,id',
            'description' => 'required',
            'supplier' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request) {
                $sparePart = SparePart::create([
                    'id' => $request->partNumber,
                    'description' => $request->description,
                    'specs' => $request->specs,
                    'supplier' => $request->supplier,
                ]);

                return response()->json([
                    'sparePart' => $sparePart,
                ]);
            });
        }
    }

    public function update(Request $request, $id) {
        $rules = [
            'partNumber' => 'required',
            'description' => 'required',
            'supplier' => 'required',
        ];

        $sparePart = SparePart::findOrFail($id);

        if($request->partNumber != $sparePart->id) {
            $rules['partNumber'] = 'required|unique:spare_parts,id';
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $sparePart) {
                $sparePart->update([
                    'id' => $request->partNumber,
                    'description' => $request->description,
                    'specs' => $request->specs,
                    'supplier' => $request->supplier,
                ]);

                return response()->json([
                    'sparePart' => $sparePart,
                ]);
            });
        }
    }

    public function delete($id) {
        $sparePart = SparePart::findOrFail($id);
        if($sparePart->delete()) {
            return response()->json([
                'id' => $id
            ]);
        }
    }
}
