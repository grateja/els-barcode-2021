<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\IncomingSparepartReport;
use Illuminate\Support\Facades\DB;

class IncomingSparepartReportController extends Controller
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
            'orderNumber' => 'required',
            'dateReceived' => 'required|date',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request) {

                $incomingReport = IncomingSparepartReport::create([
                    'date_received' => $request->dateReceived,
                    'tracking_number' => $request->trackingNumber,
                    'order_number' => $request->orderNumber,
                    'rr_number' => $request->RRNumber,
                    'pi_number' => $request->PINumber,
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
        $incomingSparepartReport = IncomingSparepartReport::findOrFail($id);

        return DB::transaction(function () use ($incomingSparepartReport, $request) {
            $incomingSparepartReport->update([
                'date_received' => $request->dateReveived,
                'tracking_number' => $request->trackingNumber,
                'order_number' => $request->orderNumber,
                'pi_number' => $request->PINumber,
                'rr_number' => $request->RRNumber,
            ]);

            return response()->json([
                'incomingReport' => $incomingSparepartReport,
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
