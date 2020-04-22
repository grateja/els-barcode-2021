<?php

namespace App\Http\Controllers\Api;

use App\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ClientsController extends Controller
{
    public function index(Request $request) {
        $result = Client::where(function($query) use ($request) {
            $query->where('owner_name', 'like', "%$request->keyword%")
                ->orWhere('shop_name', 'like', "%$request->keyword%");
        });

        return response()->json([
            'result' => $result->paginate(10),
        ]);
    }

    public function create(Request $request) {
        if($request->ownerName == null && $request->shopName == null) {
            return response()->json([
                'errors' => [
                    'message' => ['Please enter either subdealer name or company name']
                ]
            ], 422);
        }

        return DB::transaction(function () use ($request) {
            $client = Client::create([
                'owner_name' => $request->ownerName,
                'shop_name' => $request->shopName,
                'address' => $request->address,
            ]);

            return response()->json([
                'client' => $client,
            ]);
        });
    }
}
