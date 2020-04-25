<?php

namespace App\Http\Controllers\Api;

use App\ActivityLog;
use App\SparePart;
use App\SparePartItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SeriesConflict;
use Illuminate\Support\Facades\DB;

class SparePartItemsController extends Controller
{
    public function index(Request $request) {
        $result = DB::table('spare_part_items')
            ->join('spare_parts', 'spare_parts.id', '=', 'spare_part_items.spare_part_id')
            ->selectRaw('`spare_part_items`.`id` AS serial_number, `spare_parts`.`id` AS part_number, description, specs, warehouse, current_location, supplier')
            ->where(function($query) use ($request){
                $query->where(DB::raw('spare_part_items.id'), 'like', "%$request->keyword%")
                    ->orWhere(DB::raw('spare_parts.description'), 'like', "%$request->keyword%");
            });

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
                'serialNumber' => 'required|unique:spare_part_items,id',
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

                return response()->json([
                    'sparePart' => [
                        'serial_number' => $sparePartItem->id,
                        'partNumber' => $sparePartItem->partNumber,
                        'description' => $sparePartItem->description,
                        'specs' => $sparePartItem->specs,
                        'supplier' => $sparePartItem->supplier,
                        'warehouse' => $sparePartItem->warehouse,
                        'current_location' => $sparePartItem->current_location,
                    ]
                ]);
            }
        });

    }
}
