<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\OutgoingSparepartItem;

class OutgoingSparepartItemsController extends Controller
{
    public function index($outgoingSparepartReportId, Request $request)
    {
        $result = OutgoingSparepartItem::with([
            'partsItem.partsInfo'
        ])->whereHas('partsItem.partsInfo')->where('outgoing_sparepart_report_id', $outgoingSparepartReportId);

        return response()->json([
            'result' => $result->paginate(10)
        ], 200);
    }
}
