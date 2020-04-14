<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\OutgoingSparepartReport;
use App\SubDealer;
use App\Client;
use Illuminate\Support\Facades\DB;

class OutgoingSparepartReportController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            'salesInvoice' => 'required',
            'quotationNumber' => 'required',
        ];

        if($request->clientName != null && $request->clientAddress == null) {
            $rules = array_merge($rules, [
                'clientAddress' => 'required'
            ]);
        } else if($request->clientName == null && $request->clientAddress != null) {
            $rules = array_merge($rules, [
                'clientName' => 'required'
            ]);
        }

        if($request->clientName == null && $request->subdealerName == null) {
            return response()->json([
                'errors' => [
                    'clientName' => ['Client or subdealer cannot be empty'],
                    'subdealerName' => ['Client or subdealer cannot be empty'],
                ]
            ], 422);
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request) {
                $clientId = null;
                $subdealerId = null;

                if($request->clientName != null && $request->clientAddress != null) {
                    $client = Client::firstOrCreate([
                        'name' => $request->clientName,
                        'address' => $request->clientAddress,
                    ]);

                    $clientId = $client->id;
                }

                if($request->subdealerName != null && $request->subdealerAddress != null) {
                    $subdealer = SubDealer::where([
                        'name' => $request->subdealerName,
                        'address' => $request->subdealerAddress,
                    ])->first();

                    if($subdealer == null) {
                        $subdealer = SubDealer::create([
                            'name' => $request->subdealerName,
                            'address' => $request->subdealerAddress,
                            'contact_number' => $request->subdealerContactNumber,
                        ]);
                    }

                    if($subdealer != null) {
                        $subdealerId = $subdealer->id;
                    }
                }

                $outgoingReport = OutgoingSparepartReport::create([
                    'quotation_number' => $request->quotationNumber,
                    'sales_invoice' => $request->salesInvoice,
                    'dr_number' => $request->DRNumber,
                    'warranty_number' => $request->warrantyNumber,
                    'truck' => $request->truck,
                    'driver' => $request->driver,
                    'client_id' => $clientId,
                    'sub_dealer_id' => $subdealerId,
                ]);

                return response()->json([
                    'outgoingReport' => $outgoingReport,
                ], 200);
            });
        }
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'salesInvoice' => 'required',
            'quotationNumber' => 'required',
        ];

        if($request->clientName != null && $request->clientAddress == null) {
            $rules = array_merge($rules, [
                'clientAddress' => 'required'
            ]);
        } else if($request->clientName == null && $request->clientAddress != null) {
            $rules = array_merge($rules, [
                'clientName' => 'required'
            ]);
        }

        if($request->clientName == null && $request->subdealerName == null) {
            return response()->json([
                'errors' => [
                    'clientName' => ['Client or subdealer cannot be empty'],
                    'subdealerName' => ['Client or subdealer cannot be empty'],
                ]
            ], 422);
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $id) {
                $outgoingReport = OutgoingSparepartReport::findOrFail($id);
                $clientId = null;
                $subdealerId = null;

                if($request->clientName != null && $request->clientAddress != null) {
                    $client = Client::firstOrCreate([
                        'name' => $request->clientName,
                        'address' => $request->clientAddress,
                    ]);

                    $clientId = $client->id;
                }

                if($request->subdealerName != null && $request->subdealerAddress != null) {
                    $subdealer = SubDealer::where([
                        'name' => $request->subdealerName,
                        'address' => $request->subdealerAddress,
                    ])->first();

                    if($subdealer == null) {
                        $subdealer = SubDealer::create([
                            'name' => $request->subdealerName,
                            'address' => $request->subdealerAddress,
                            'contact_number' => $request->subdealerContactNumber,
                        ]);
                    }

                    if($subdealer != null) {
                        $subdealerId = $subdealer->id;
                    }
                }

                $outgoingReport->update([
                    'quotation_number' => $request->quotationNumber,
                    'sales_invoice' => $request->salesInvoice,
                    'dr_number' => $request->DRNumber,
                    'warranty_number' => $request->warrantyNumber,
                    'truck' => $request->truck,
                    'driver' => $request->driver,
                    'client_id' => $clientId,
                    'sub_dealer_id' => $subdealerId,
                ]);

                return response()->json([
                    'outgoingReport' => $outgoingReport,
                ], 200);
            });
        }
    }
}
