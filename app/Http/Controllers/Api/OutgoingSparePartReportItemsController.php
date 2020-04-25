<?php

namespace App\Http\Controllers\Api;

use App\SparePartItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\OutgoingSparePartReport;
use Illuminate\Support\Facades\DB;

class OutgoingSparePartReportItemsController extends Controller
{
    public function index($reportId) {
        $result = SparePartItem::with('sparePart')->whereHas('outgoingSparePartReports', function($query) use ($reportId) {
            $query->where('outgoing_report_id', $reportId);
        })->get();

        $result = collect($result)->transform(function($item) {
            return [
                'serial_number' => $item->id,
                'model' => $item->model,
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
                $report = OutgoingSparePartReport::findOrFail($reportId);

                $newIds = $report->attachItems($request->serialNumbers);
                $sparePartItems = SparePartItem::with('sparePart')->whereIn('id', $newIds)->get();
                $items= $sparePartItems->transform(function($item) {
                    return [
                        'serial_number' => $item->id,
                        'model' => $item->model,
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
            $report = OutgoingSparePartReport::findOrFail($reportId);

            $report->SparePartItems()->detach($request->serialNumbers);

            return response()->json([
                'serialNumbers' => $request->serialNumbers,
            ]);
        });
    }
}
