<?php

namespace App\Http\Controllers\Api;

use App\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AccountsController extends Controller
{
    public function autocomplete(Request $request) {
        $result = Account::where('id', 'like', "%$request->keyword%")
            ->orWhere('name', 'like', "%$request->keyword%")
            ->select('*', DB::raw('CONCAT("[", `id`, "] / ", `name`, " / ", `department`) as display'))
            ->limit(10)
            ->get();

        return response()->json([
            'data' => $result,
        ], 200);
    }
}
