<?php

namespace App\Http\Controllers\Api;

use App\Account;
use App\FixedAsset;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FixedAssetItemsController extends Controller
{
    public function index(Request $request, $accountId = null) {
        $result = DB::table('fixed_assets')
            ->leftjoin('accounts', 'accounts.id', '=', 'fixed_assets.account_id')
            ->selectRaw('fixed_assets.id as serial_number, description, specs, fixed_assets.created_at as date_issued, IFNULL(accounts.name, "(Not issued)") as account_name, department, account_id')
            ->where(function($query) use ($request) {
                $query->where(DB::raw('fixed_assets.id'), 'like', "%$request->keyword%")
                    ->orWhere(DB::raw('description'), 'like', "%$request->keyword%")
                    ->orWhere(DB::raw('accounts.name'), 'like', "%$request->keyword%")
                    ->orWhere(DB::raw('accounts.department'), 'like', "%$request->keyword%")
                    ->orWhere(DB::raw('specs'), 'like', "%$request->keyword%");
            })->whereNull(DB::raw('fixed_assets.deleted_at'));

        if($accountId) {
            $result = $result->where('account_id', $accountId);
        }

        if($orderBy = FixedAsset::canOrderBy($request->sortBy)) {
            $result = $result->orderBy($orderBy, $request->orderBy);
        }

        return response()->json([
            'result' => $result->paginate(10),
        ]);
    }

    public function create(Request $request) {
        $rules = [
            'employeeId' => 'required',
            'name' => 'required',
            'department' => 'required',
            'serialNumber' => 'required|unique:fixed_assets,id',
            'description' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request) {
                $account = Account::find($request->employeeId);

                if($account == null) {
                    $account = Account::create([
                        'id' => $request->employeeId,
                        'name' => $request->name,
                        'department' => $request->department,
                    ]);
                } else {
                    $account->update([
                        'name' => $request->name,
                        'department' => $request->department,
                    ]);
                }

                $fixedAsset = FixedAsset::create([
                    'id' => $request->serialNumber,
                    'description' => $request->description,
                    'specs' => $request->specs,
                    'created_at' => $request->dateIssued,
                    'account_id' => $account->id,
                ]);

                return response()->json([
                    'fixedAsset' => [
                        'serial_number' => $fixedAsset->serial_number,
                        'description' => $fixedAsset->description,
                        'specs' => $fixedAsset->specs,
                        'account_name' => $fixedAsset->account_name,
                        'department' => $fixedAsset->department,
                        'account_id' => $fixedAsset->account_id,
                        'date_issued' => $fixedAsset->date_issued,
                    ]
                ]);
            });
        }
    }

    public function update(Request $request, $serialNumber) {
        $rules = [
            'employeeId' => 'required',
            'name' => 'required',
            'department' => 'required',
            'serialNumber' => 'required',
            'description' => 'required',
        ];

        $fixedAsset = FixedAsset::findOrFail($serialNumber);

        if($fixedAsset->id != $request->serialNumber) {
            $rules['serialNumber'] = 'required|unique:fixed_assets,id';
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $fixedAsset) {
                $account = Account::find($request->employeeId);

                if($account == null) {
                    $account = Account::create([
                        'id' => $request->employeeId,
                        'name' => $request->name,
                        'department' => $request->department,
                    ]);
                } else {
                    $account->update([
                        'name' => $request->name,
                        'department' => $request->department,
                    ]);
                }

                $fixedAsset->update([
                    'id' => $request->serialNumber,
                    'description' => $request->description,
                    'specs' => $request->specs,
                    'created_at' => $request->dateIssued,
                    'account_id' => $account->id,
                ]);

                return response()->json([
                    'fixedAsset' => [
                        'serial_number' => $fixedAsset->serial_number,
                        'description' => $fixedAsset->description,
                        'specs' => $fixedAsset->specs,
                        'account_name' => $fixedAsset->account_name,
                        'department' => $fixedAsset->department,
                        'account_id' => $fixedAsset->account_id,
                        'date_issued' => $fixedAsset->date_issued,
                    ]
                ]);
            });
        }
    }

    public function insertSerial(Request $request, $accountId) {
        $rules = [
            'serialNumber' => 'required|unique:fixed_assets,id',
            'description' => 'required',
            'dateIssued' => 'required|date',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $accountId) {
                $fixedAsset = FixedAsset::create([
                    'account_id' => $accountId,
                    'id' => $request->serialNumber,
                    'description' => $request->description,
                    'specs' => $request->description,
                    'created_at' => $request->issued,
                ]);

                return response()->json([
                    'fixedAsset' => $fixedAsset,
                ]);
            });
        }
    }

    public function updateSerial(Request $request, $serialNumber) {
        $rules = [
            'serialNumber' => 'required',
            'description' => 'required',
            'dateIssued' => 'required|date',
        ];

        $fixedAsset = FixedAsset::findOrFail($serialNumber);
        if($fixedAsset->id != $request->serialNumber) {
            $rules['serialNumber'] = 'required|unique:fixed_assets,id';
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $fixedAsset) {
                $fixedAsset->update([
                    'id' => $request->serialNumber,
                    'description' => $request->description,
                    'specs' => $request->description,
                    'created_at' => $request->issued,
                ]);

                return response()->json([
                    'fixedAsset' => $fixedAsset,
                ]);
            });
        }
    }

    public function removeFromAccount($accountId, $serialNumber) {
        return DB::transaction(function () use ($accountId, $serialNumber) {
            $account = Account::findOrFail($accountId);

            $fixedAsset = FixedAsset::findOrFail($serialNumber);
            $fixedAsset->update([
                'account_id' => null,
            ]);

            return response()->json([
                'serialNumber' => $serialNumber,
            ]);
        });
    }

    public function delete($serialNumber) {
        return DB::transaction(function() use ($serialNumber) {
            $fixedAsset = FixedAsset::findOrFail($serialNumber);

            if($fixedAsset->delete()) {
                return response()->json([
                    'fixedAsset' => $fixedAsset,
                ]);
            }
        });
    }
}
