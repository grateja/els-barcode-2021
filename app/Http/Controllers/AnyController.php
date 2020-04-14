<?php

namespace App\Http\Controllers;

use App\FinishedGoodItem;
use Illuminate\Http\Request;

class AnyController extends Controller
{
    public function show($code) {
        $item = FinishedGoodItem::where('serial_number', $code)->first();
    }
}
