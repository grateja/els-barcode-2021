<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\OutgoingFinishedGoodReport;
use App\Reservation;
use Illuminate\Support\Facades\DB;

class OutgoingFinishedGoodsController extends Controller
{
    public function index(Request $request) {
        $result = DB::table('outgoing_finished_good_reports')
            ->leftjoin('clients', 'clients.id', '=', 'outgoing_finished_good_reports.client_id')
            ->leftjoin('subdealers', 'subdealers.id', '=', 'outgoing_finished_good_reports.subdealer_id')
            ->leftjoin('reservations', 'reservations.id', '=', 'outgoing_finished_good_reports.reservation_id')
            ->where(function($query) use ($request) {
                $query->where('owner_name', 'like', "%$request->keyword%")
                ->orWhere('subdealer_name', 'like', "%$request->keyword%")
                ->orWhere('quotation_number', 'like', "%$request->keyword%")
                ->orWhere('dr_number', 'like', "%$request->keyword%")
                ->orWhere('warranty_number', 'like', "%$request->keyword%")
                ->orWhere('sales_invoice', 'like', "%$request->keyword%");
            })->selectRaw('*, outgoing_finished_good_reports.id as id, COALESCE(`clients`.`owner_name`, `clients`.`shop_name`) as owner_name, COALESCE(`subdealers`.`subdealer_name`, `subdealers`.`company_name`) as subdealer_name');


        // if($request->date) {
        //     $result = $result->whereDate('created_at', $request->date);
        // }

        $result = $result->orderBy($request->sortBy, $request->orderBy);

        return response()->json([
            'result' => $result->paginate(10),
        ]);
    }

    public function show($reportId) {
        $report = OutgoingFinishedGoodReport::with('client', 'subdealer', 'reservation')->findOrFail($reportId);

        return response()->json([
            'report' => $report,
        ]);
    }

    public function delete($reportId) {
        $report = OutgoingFinishedGoodReport::findOrFail($reportId);

        if($report->delete()) {
            return response()->json([
                'reportId' => $reportId,
            ]);
        }
    }

    public function create(Request $request) {
        $reservation = null;
        if($request->clientId == null && $request->subdealerId == null) {
            return response()->json([
                'errors' => [
                    'message' => ['Please enter at least subdealer or client']
                ]
            ], 422);
        }

        if($request->referenceNumber) {
            $reservation = Reservation::where('reference_number', $request->referenceNumber)->first();
            if($reservation == null) {
                return response()->json([
                    'errors' => [
                        'referenceNumber' => ['This reference number is not in resevations']
                    ]
                ], 422);
            }
        }

        $rules = [
            'salesInvoice' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $reservation) {
                $report = OutgoingFinishedGoodReport::create([
                    'client_id' => $request->clientId,
                    'subdealer_id' => $request->subdealerId,
                    'reservation_id' => $reservation ? $reservation->id : null,
                    'date_delivered' => $request->dateDelivered,
                    'po_date' => $request->poDate,
                    'downpayment_date' => $request->downpaymentDate,
                    'invoice_date' => $request->invoiceDate,
                    'quotation_number' => $request->quotationNumber,
                    'sales_invoice' => $request->salesInvoice,
                    'dr_number' => $request->drNumber,
                    'warranty_number' => $request->warrantyNumber,
                ]);

                return response()->json([
                    'report' => [
                        'owner_name' => $report->client ? $report->client->owner_name : null,
                        'subdealer_name' => $report->subdealer ? $report->subdealer->subdealer_name : null,
                        'reference_number' => $report->reservation ? $report->reservation->reference_number : null,
                        'date_delivered' => $report->dateDelivered,
                        'po_date' => $report->poDate,
                        'downpayment_date' => $report->downpaymentDate,
                        'invoice_date' => $report->invoiceDate,
                        'quotation_number' => $report->quotationNumber,
                        'sales_invoice' => $report->salesInvoice,
                        'dr_number' => $report->drNumber,
                        'warranty_number' => $report->warrantyNumber,
                    ],
                ]);
            });
        }
    }

    public function update(Request $request, $reportId) {
        if($request->clientId == null && $request->subdealerId == null) {
            return response()->json([
                'errors' => [
                    'message' => ['Please enter at least subdealer or client']
                ]
            ], 422);
        }

        if($request->referenceNumber) {
            $reservation = Reservation::where('reference_number', $request->referenceNumber)->first();
            if($reservation == null) {
                return response()->json([
                    'errors' => [
                        'referenceNumber' => ['This reference number is not in resevations']
                    ]
                ], 422);
            }
        }

        $rules = [
            'salesInvoice' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $reportId, $reservation) {
                $report = OutgoingFinishedGoodReport::findOrFail($reportId);

                $report->update([
                    'client_id' => $request->clientId,
                    'subdealer_id' => $request->subdealerId,
                    'reservation_id' => $reservation ? $reservation->id : null,
                    'date_delivered' => $request->dateDelivered,
                    'po_date' => $request->poDate,
                    'downpayment_date' => $request->downpaymentDate,
                    'invoice_date' => $request->invoiceDate,
                    'quotation_number' => $request->quotationNumber,
                    'sales_invoice' => $request->salesInvoice,
                    'dr_number' => $request->drNumber,
                    'warranty_number' => $request->warrantyNumber,
                    'truck' => $request->truck,
                    'driver' => $request->driver,
                ]);

                return response()->json([
                    'report' => [
                        'owner_name' => $report->client ? $report->client->owner_name : null,
                        'subdealer_name' => $report->subdealer ? $report->subdealer->subdealer_name : null,
                        'reference_number' => $report->reservation ? $report->reservation->reference_number : null,
                        'date_delivered' => $report->dateDelivered,
                        'po_date' => $report->poDate,
                        'downpayment_date' => $report->downpaymentDate,
                        'invoice_date' => $report->invoiceDate,
                        'quotation_number' => $report->quotationNumber,
                        'sales_invoice' => $report->salesInvoice,
                        'dr_number' => $report->drNumber,
                        'warranty_number' => $report->warrantyNumber,
                        'truck' => $report->truck,
                        'driver' => $report->driver,
                    ],
                ]);
            });
        }
    }
}
