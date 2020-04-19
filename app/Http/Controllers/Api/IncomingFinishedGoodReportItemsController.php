<?php

namespace App\Http\Controllers\Api;

use App\FinishedGoodItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\IncomingFinishedGoodReport;
use Illuminate\Support\Facades\DB;

class IncomingFinishedGoodReportItemsController extends Controller
{
    public function index($reportId) {
        $result = FinishedGoodItem::with('finishedGood')->whereHas('incomingFinishedGoodReports', function($query) use ($reportId) {
            $query->where('incoming_report_id', $reportId);
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
                $report = IncomingFinishedGoodReport::findOrFail($reportId);

                $newIds = $report->attachItems($request->serialNumbers);
                $finishedGoodItems = FinishedGoodItem::with('finishedGood')->whereIn('id', $newIds)->get();
                $items= $finishedGoodItems->transform(function($item) {
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
            $report = IncomingFinishedGoodReport::findOrFail($reportId);

            $report->finishedGoodItems()->detach($request->serialNumbers);

            return response()->json([
                'serialNumbers' => $request->serialNumbers,
            ]);
        });
    }
}
