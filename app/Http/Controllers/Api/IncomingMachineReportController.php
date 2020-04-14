<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\IncomingMachineReport;
use Illuminate\Support\Facades\DB;

class IncomingMachineReportController extends Controller
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
    public function store(Request $request) {
        $rules = [
            'PONumber' => 'required',
            'dateReceived' => 'required|date',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request) {

                $incomingReport = IncomingMachineReport::create([
                    'date_received' => $request->dateReceived,
                    'po_number' => $request->PONumber,
                    'rr_number' => $request->RRNumber,
                    'dr_number' => $request->DRNumber,
                    'pi_number' => $request->PINumber,
                    'billing_number' => $request->billNumber,
                    'truck_number' => $request->truckNumber,
                ]);

                return response()->json([
                    'incomingReport' => $incomingReport,
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
        $incomingMachineReport = IncomingMachineReport::findOrFail($id);

        return DB::transaction(function () use ($incomingMachineReport, $request) {
            $incomingMachineReport->update([
                'date_received' => $request->dateReveived,
                'po_number' => $request->PONumber,
                'rr_number' => $request->RRNumber,
                'dr_number' => $request->DRNumber,
                'pi_number' => $request->PINumber,
                'billing_number' => $request->billNumber,
                'truck_number' => $request->truckNumber,
            ]);

            return response()->json([
                'incomingReport' => $incomingMachineReport,
            ], 200);
        });

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
