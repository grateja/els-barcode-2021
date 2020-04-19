<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\IncomingFinishedGoodReport;
use Illuminate\Support\Facades\DB;

class IncomingFinishedGoodsController extends Controller
{
    public function index(Request $request) {
        $result = IncomingFinishedGoodReport::where(function($query) use ($request) {
            $query->where('po_number', 'like', "%$request->keyword%")
                ->orWhere('rr_number', 'like', "%$request->keyword%")
                ->orWhere('pi_number', 'like', "%$request->keyword%");
        });

        if($request->date) {
            $result = $result->whereDate('created_at', $request->date);
        }

        $result = $result->orderBy($request->sortBy, $request->orderBy);

        return response()->json([
            'result' => $result->paginate(10),
        ]);
    }

    public function delete($reportId) {
        $report = IncomingFinishedGoodReport::findOrFail($reportId);

        if($report->delete()) {
            return response()->json([
                'reportId' => $reportId,
            ]);
        }
    }

    public function create(Request $request) {
        $rules = [
            'receivedDate' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request) {
                $report = IncomingFinishedGoodReport::create([
                    'received_date' => $request->receivedDate,
                    'po_number' => $request->poNumber,
                    'rr_number' => $request->rrNumber,
                    'pi_number' => $request->piNumber,
                    'truck_number' => $request->truckNumber,
                ]);

                return response()->json([
                    'report' => $report,
                ]);
            });
        }
    }

    public function update(Request $request, $reportId) {
        $rules = [
            'receivedDate' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $reportId) {
                $report = IncomingFinishedGoodReport::findOrFail($reportId);

                $report->update([
                    'received_date' => $request->receivedDate,
                    'po_number' => $request->poNumber,
                    'rr_number' => $request->rrNumber,
                    'pi_number' => $request->piNumber,
                    'truck_number' => $request->truckNumber,
                ]);

                return response()->json([
                    'report' => $report,
                ]);
            });
        }
    }
}
