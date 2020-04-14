<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportTemplate;

class ExcelReportsController extends Controller
{
    public function posProducts(Request $request) {
        $result = [];
        return Excel::download(new ReportTemplate($result, [
            'JOB ORDER',
            'CUSTOMER NAME',
            'PRODUCT NAME',
            'UNIT PRICE',
            'QUANTITY',
            'DATE PAID',
            'PAID TO',
            'ADDED BY',
        ]), 'parts.xls');
    }
}
