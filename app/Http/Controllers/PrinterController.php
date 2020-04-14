<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrinterController extends Controller
{
    public function jobOrder() {
        $data = [
        ];
        return view('printer.job-order', $data);
    }
}
