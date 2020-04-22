<?php

namespace App\Http\Controllers\Api;

use App\SparePartItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\IncomingSparePartReport;
use Illuminate\Support\Facades\DB;

class IncomingSparePartReportItemsController extends Controller
{
    public function index($reportId) {
        $result = SparePartItem::with('sparePart')->whereHas('incomingSparePartReports', function($query) use ($reportId) {
            $query->where('incoming_report_id', $reportId);
        })->get();

        $result = collect($result)->transform(function($item) {
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
                $report = IncomingSparePartReport::findOrFail($reportId);

                $newIds = $report->attachItems($request->serialNumbers);
                $sparePartItems = SparePartItem::with('sparePart')->whereIn('id', $newIds)->get();
                $items= $sparePartItems->transform(function($item) {
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
            $report = IncomingSparePartReport::findOrFail($reportId);

            $report->sparePartItems()->detach($request->serialNumbers);

            return response()->json([
                'serialNumbers' => $request->serialNumbers,
            ]);
        });
    }
}
