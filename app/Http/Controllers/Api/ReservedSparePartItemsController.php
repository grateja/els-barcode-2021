<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Reservation;
use App\SparePartItem;
use Illuminate\Support\Facades\DB;

class ReservedSparePartItemsController extends Controller
{
    public function index($reportId) {
        $result = SparePartItem::with('sparePart')->whereHas('reservations', function($query) use ($reportId) {
            $query->where('reservation_id', $reportId);
        })->paginate(10);

        $result->getCollection()->transform(function($item) {
            return [
                'serial_number' => $item->id,
                'part_number' => $item->part_number,
                'description' => $item->description,
                'specs' => $item->specs,
                'supplier' => $item->supplier,
                'warehouse' => $item->warehouse,
                'current_location' => $item->current_location,
            ];
        });

        return response()->json([
            'result' => $result,
        ]);
    }

    public function addItems(Request $request, $reportId) {
        $rules = [
            'serialNumbers' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $reportId) {
                $report = Reservation::findOrFail($reportId);

                $newIds = $report->attachSpareParts($request->serialNumbers);
                $sparePartItems = SparePartItem::with('sparePart')->whereIn('id', $newIds)->get();
                $items = $sparePartItems->transform(function($item) {
                    return [
                        'serial_number' => $item->id,
                        'part_number' => $item->part_number,
                        'description' => $item->description,
                        'specs' => $item->specs,
                        'supplier' => $item->supplier,
                        'warehouse' => $item->warehouse,
                        'current_location' => $item->current_location,
                    ];
                });
                return response()->json([
                    'items' => $items,
                ]);
            });
        }
    }

    public function removeItems(Request $request, $reportId) {
        return DB::transaction(function () use ($request, $reportId) {
            $report = Reservation::findOrFail($reportId);

            $report->spareParts()->detach($request->serialNumbers);

            return response()->json([
                'serialNumbers' => $request->serialNumbers,
            ]);
        });
    }
}
