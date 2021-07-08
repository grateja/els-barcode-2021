<?php

namespace App\Http\Controllers\Api;

use App\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FixedAssetAccountsController extends Controller
{
    public function index(Request $request) {
        $result = Account::withCount('fixedAssets')->where(function($query) use ($request) {
            $query->where('name', 'like', "%$request->keyword%")
                ->orWhere('department', 'like', "%$request->keyword%");
        });

        return response()->json([
            'result' => $result->paginate(10),
        ]);
    }

    public function create(Request $request) {
        $rules = [
            'employeeId' => 'required|unique:accounts,id',
            'name' => 'required',
            'department' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request) {
                $account = Account::create([
                    'id' => $request->employeeId,
                    'name' => $request->name,
                    'department' => $request->department,
                ]);

                return response()->json([
                    'account' => $account,
                ]);
            });
        }
    }

    public function update(Request $request, $accountId) {
        $rules = [
            'employeeId' => 'required|unique:accounts,id',
            'name' => 'required',
            'department' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $accountId) {
                $account = Account::findOrFail($accountId);

                $account->update([
                    'id' => $request->employeeId,
                    'name' => $request->name,
                    'department' => $request->department,
                ]);

                return response()->json([
                    'account' => $account,
                ]);
            });
        }
    }

    public function delete($accountId) {
        return DB::transaction(function () use ($accountId) {
            $account = Account::findOrFail($accountId);
            if($account->delete()) {
                return response()->json([
                    'id' => $accountId,
                ]);
            }
        });
    }
}
