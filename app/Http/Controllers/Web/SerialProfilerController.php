<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SerialNumberProfiler;

class SerialProfilerController extends Controller
{
    public function show($code) {
        $profiler = SerialNumberProfiler::findByCode($code);

        if($profiler == null) {
            return view('serial-profiler.item-not-exists', [
                'code' => $code,
            ]);
        }

        return redirect(route($profiler->redirect, $code));
    }
}
