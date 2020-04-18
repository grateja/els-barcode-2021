<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FixedAssetsController extends Controller
{
    public function generateSerial(Request $request) {
        if($request->description) {
            $noSpaces = preg_replace('/\s+/', '', $request->description);
            $result = str_shuffle($noSpaces);
        } else {
            $result = str_random(10);
        }
        return response()->json(strtoupper($result));
    }
}
