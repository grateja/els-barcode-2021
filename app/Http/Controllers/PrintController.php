<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PartsItem;

class PrintController extends Controller
{
    public function unique(Request $request) {
        if(!$request->selectedItem) {
            if($request->ajax()) {
                return response()->json([
                    'No selected item',
                ], 422);
            }

            return redirect()->back()->withErrors([
                'selectedItem' => ['No selected items']
            ]);
        }

        $result = PartsItem::with([
            'partsInfo' => function($query) {
                $query->select('id', 'code', 'description');
            }
        ])->whereIn('id', $request->selectedItem)
        ->select('id', 'serial_number', 'parts_info_id')->get();

        $result = collect($result)->map(function($item) {
            $obj = [
                'description' => $item->partsInfo->description,
                'code' => $item->serial_number,
            ];

            return $obj;
        });

        return view('print.unique', ['items' => $result]);
    }
}
