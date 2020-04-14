<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\OutgoingMachineReport;
use App\Client;
use App\SubDealer;

class OutgoingMachineReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'DRNumber' => 'required',
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

                $outgoingReport = OutgoingMachineReport::create([
                    'downpayment_date' => $request->downpaymentDate,
                    'po_date' => $request->PODate,
                    'invoice_date' => $request->invoiceDate,
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'DRNumber' => 'required',
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
                $outgoingReport = OutgoingMachineReport::findOrFail($id);
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
                    'downpayment_date' => $request->downpaymentDate,
                    'po_date' => $request->PODate,
                    'invoice_date' => $request->invoiceDate,
                    'quotation_number' => $request->quotationNumber,
                    'sales_invoice' => $request->salesInvoice,
                    'dr_number' => $request->DRNumber,
                    'warranty_number' => $request->warrantyNumber,
                    'truck' => $request->truck,
                    'driver' => $request->driver,
                    'client_id' => $clientId,
                    'sub_dealer_id' => $subdealerId,
                ]);

                // $outgoingReport = OutgoingMachineReport::create([
                //     'downpayment_date' => $request->downpaymentDate,
                //     'po_date' => $request->PODate,
                //     'invoice_date' => $request->invoiceDate,
                //     'quotation_number' => $request->quotationNumber,
                //     'sales_invoice' => $request->salesInvoice,
                //     'dr_number' => $request->DRNumber,
                //     'warranty_number' => $request->warrantyNumber,
                //     'truck' => $request->truck,
                //     'driver' => $request->driver,
                //     'client_id' => $clientId,
                //     'sub_dealer_id' => $subdealerId,
                // ]);

                return response()->json([
                    'outgoingReport' => $outgoingReport,
                ], 200);
            });
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
