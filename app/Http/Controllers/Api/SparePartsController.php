<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SparePart;
use Illuminate\Support\Facades\DB;

class SparePartsController extends Controller
{
    public function autocomplete(Request $request) {
        $result = SparePart::where('id', 'like', "%$request->keyword%")
            ->orWhere('description', 'like', "%$request->keyword%")
            ->select('*', DB::raw('`id` as partNumber, CONCAT(`id`, " - ", `description`, IF(`specs` <> "", CONCAT(" - (", `specs`, ")"), "")) as display, supplier'))
            ->limit(10)
            ->get();

        return response()->json([
            'data' => $result,
        ], 200);
    }
}
