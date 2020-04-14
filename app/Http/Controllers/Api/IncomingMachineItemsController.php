<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\IncomingMachineItem;
use App\PartsItem;

class IncomingMachineItemsController extends Controller
{
    public function index($incomingMachineReportId, Request $request) {
        $result = IncomingMachineItem::with([
            'partsItem.partsInfo'
        ])->whereHas('partsItem.partsInfo')->where('incoming_machine_report_id', $incomingMachineReportId);



        return response()->json([
            'result' => $result->paginate(10)
        ], 200);
    }
}
