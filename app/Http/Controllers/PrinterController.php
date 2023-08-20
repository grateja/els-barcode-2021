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

        if($result->count() % 2 == 1) {
            return "Cannot print {$result->count()} items. Select 1 more or select 1 less";
        }

        return view('printer.unique', ['items' => $result]);
    }
}
