<?php

namespace App\Http\Controllers;

use App\SerialNumberProfiler;
use Illuminate\Http\Request;

class PrinterController extends Controller
{
    public function serialNumbers(Request $request) {
        if(!$request->serialNumbers) {
            if($request->ajax()) {
                return response()->json([
                    'No selected item',
                ], 422);
            }

            return redirect()->back()->withErrors([
                'serialNumbers' => ['No selected items']
            ]);
        }

        $result = SerialNumberProfiler::whereIn('id', $request->serialNumbers)
            ->select('id', 'barcode_label')->get();

        return view('printer.unique', ['items' => $result]);
    }
}
