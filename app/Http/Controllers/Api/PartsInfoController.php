<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PartsInfo;
use Illuminate\Support\Facades\DB;
use App\PartsInfoLog;
use App\Supplier;

class PartsInfoController extends Controller
{

    public function index(Request $request) {
        $result = PartsInfo::with('supplier')
            ->where(function($query) use ($request) {
                $query->where('code', 'like', "%$request->keyword%")
                ->orWhere('description', 'like', "%$request->keyword%");
            })
            ->withCount('partsItems as total');

        if($request->supplierId) {
            $result = $result->where('supplier_id', $request->supplierId);
        }

        if($request->itemType) {
            $result = $result->where('item_type', $request->itemType);
        }
        return response()->json([
            'result' => $result->orderByDesc('updated_at')->paginate(10),
        ], 200);
    }

    public function show($id) {
        $partsInfo = PartsInfo::with('supplier')->findOrFail($id);
        return response()->json([
            'partsInfo' => $partsInfo,
        ], 200);
    }

    public function update($id, Request $request) {
        $partsInfo = PartsInfo::findOrFail($id);

        $rules = [
            'code' => 'required',
            'description' => 'required',
            'supplier' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $partsInfo) {

                if(is_array($request->supplier)) {
                    $supplierId = $request->supplier['id'];
                } else if(is_string($request->supplier)) {
                    $supplier = Supplier::firstOrCreate([
                        'name' => $request->supplier,
                    ]);
                    $supplierId = $supplier->id;
                } else {
                    $supplierId = null;
                }


                $partsInfo->update([
                    'code' => $request->code,
                    'description' => $request->description,
                    'specs' => $request->specs,
                    'model' => $request->model,
                    'supplier_id' => $supplierId,
                    'item_type' => $request->itemType
                ]);

                // PartsInfoLog::insert([
                //     'user_id' => auth('api')->id(),
                //     'title' => 'updated Parts Profile',
                //     'parts_info_id' => $partsInfo->id,
                // ]);
            });
        }
    }

    public function store(Request $request) {
        $rules = [
            'code' => 'required',
            'description' => 'required',
            'supplier' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request) {

                if(is_array($request->supplier)) {
                    $supplierId = $request->supplier['id'];
                } else if(is_string($request->supplier)) {
                    $supplier = Supplier::firstOrCreate([
                        'name' => $request->supplier,
                    ]);
                    $supplierId = $supplier->id;
                } else {
                    $supplierId = null;
                }


                $partsInfo = PartsInfo::create([
                    'code' => $request->code,
                    'description' => $request->description,
                    'specs' => $request->specs,
                    'model' => $request->model,
                    'supplier_id' => $supplierId,
                    'item_type' => $request->itemType
                ]);
            });
        }
    }

    public function delete($id) {
        $partsInfo = PartsInfo::findOrFail($id);

        return DB::transaction(function () use ($id, $partsInfo) {
            if($partsInfo->delete()) {
                return response()->json([
                    'message' => 'Deleted successfully.',
                    'id' => $id
                ], 200);
            }
        });
    }
}
