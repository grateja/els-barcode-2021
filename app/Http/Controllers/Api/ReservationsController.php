<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Reservation;

class ReservationsController extends Controller
{
    public function index(Request $request) {
        $result = DB::table('reservations')
            ->leftjoin('clients', 'clients.id', '=', 'reservations.client_id')
            ->leftjoin('subdealers', 'subdealers.id', '=', 'reservations.subdealer_id')
            ->where(function($query) use ($request) {
                $query->where('reference_number', 'like', "%$request->keyword%")
                    ->orWhere(DB::raw('clients.owner_name'), 'like', "%$request->keyword%")
                    ->orWhere(DB::raw('subdealers.subdealer_name'), 'like', "%$request->keyword%");
            })->selectRaw('reservations.id as id, reference_number, downpayment_date, owner_name, subdealer_name, remarks');

        return response()->json([
            'result' => $result->paginate(10),
        ]);
    }

    public function show($reportId) {
        $report = Reservation::with('subdealer', 'client')->findOrFail($reportId);

        return response()->json([
            'report' => $report
        ]);
    }

    public function create(Request $request) {
        if($request->clientId == null && $request->subdealerId == null) {
            return response()->json([
                'errors' => [
                    'message' => ['Please enter either subdealer or client.'],
                ]
            ], 422);
        }

        return DB::transaction(function () use ($request) {
            $report = Reservation::create([
                'downpayment_date' => $request->downpaymentDate,
                'reference_number' => $request->referenceNumber,
                'client_id' => $request->clientId,
                'subdealer_id' => $request->subdealerId,
                'remarks' => $request->remarks,
            ]);

            return response()->json([
                'report' => [
                    'id' => $report->id,
                    'reference_number' => $report->reference_number,
                    'downpayment_date' => $report->downpayment_date,
                    'owner_name' => $report->owner_name,
                    'subdealer_name' => $report->subdealer_name,
                    'remarks' => $report->remarks,
                ],
            ]);
        });
    }

    public function update(Request $request, $reportId) {
        if($request->clientId == null && $request->subdealerId == null) {
            return response()->json([
                'errors' => [
                    'message' => ['Please enter either subdealer or client.'],
                ]
            ], 422);
        }

        return DB::transaction(function () use ($request, $reportId) {
            $report = Reservation::findOrFail($reportId);
            $report->update([
                'downpayment_date' => $request->downpaymentDate,
                'reference_number' => $request->referenceNumber,
                'client_id' => $request->clientId,
                'subdealer_id' => $request->subdealerId,
                'remarks' => $request->remarks,
            ]);

            return response()->json([
                'report' => [
                    'id' => $report->id,
                    'reference_number' => $report->reference_number,
                    'downpayment_date' => $report->downpayment_date,
                    'owner_name' => $report->owner_name,
                    'subdealer_name' => $report->subdealer_name,
                    'remarks' => $report->remarks,
                ],
            ]);
        });
    }
}
