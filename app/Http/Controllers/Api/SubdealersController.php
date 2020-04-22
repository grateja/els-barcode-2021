<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Subdealer;
use Illuminate\Support\Facades\DB;

class SubdealersController extends Controller
{
    public function index(Request $request) {
        $result = Subdealer::where(function($query) use ($request) {
            $query->where('subdealer_name', 'like', "%$request->keyword%")
                ->orWhere('company_name', 'like', "%$request->keyword%");
        });

        return response()->json([
            'result' => $result->paginate(10),
        ]);
    }

    public function create(Request $request) {
        if($request->subdealerName == null && $request->companyName == null) {
            return response()->json([
                'errors' => [
                    'message' => ['Please enter either subdealer name or company name']
                ]
            ], 422);
        }

        return DB::transaction(function () use ($request) {
            $subdealer = Subdealer::create([
                'subdealer_name' => $request->subdealerName,
                'company_name' => $request->companyName,
                'address' => $request->address,
            ]);

            return response()->json([
                'subdealer' => $subdealer,
            ]);
        });
    }
}
