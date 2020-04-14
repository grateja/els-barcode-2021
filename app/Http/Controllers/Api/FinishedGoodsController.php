<?php

namespace App\Http\Controllers\Api;

use App\FinishedGood;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FinishedGoodsController extends Controller
{
    public function autocomplete(Request $request) {
        $result = FinishedGood::where('model', 'like', "%$request->keyword%")
            ->orWhere('description', 'like', "%$request->keyword%")
            ->select('*', DB::raw('CONCAT(`model`, " - ", `description`, IF(`specs` <> "", CONCAT(" - (", `specs`, ")"), "")) as display, supplier'))
            ->limit(10)
            ->get();

        return response()->json([
            'data' => $result,
        ], 200);
    }
}
