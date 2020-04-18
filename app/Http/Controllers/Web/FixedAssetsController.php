<?php

namespace App\Http\Controllers\Web;

use App\Account;
use App\FixedAsset;
use App\FixedAssetRemarks;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tag;
use Illuminate\Support\Facades\DB;

class FixedAssetsController extends Controller
{
    public function show($serialNumber) {
        $fixedAsset = FixedAsset::withTrashed()->find($serialNumber);

        if($fixedAsset == null) {
            $model = null;
        } else {
            $model = [
                'serialNumber' => $serialNumber,
                'accountName' => $fixedAsset->account_name,
                'department' => $fixedAsset->department,
                'description' => $fixedAsset->description,
                'specs' => $fixedAsset->specs,
                'tags' => $fixedAsset->tags_str,
                'dateIssued' => $fixedAsset->date_issued,
                'deletedAt' => $fixedAsset->deleted_at,
                'remarks' => $fixedAsset->fixedAssetRemarks,
            ];
        }
        return view('fixed-assets.view-item', [
            'model' => $model,
            'serialNumber' => $serialNumber,
        ]);
    }

    public function addToInventory($serialNumber) {
        $fixedAsset = FixedAsset::find($serialNumber);

        return view('fixed-assets.add-to-inventory', [
            'serialNumber' => $fixedAsset ? $fixedAsset->id : $serialNumber,
            'description' => $fixedAsset ? $fixedAsset->description : null,
            'specs' => $fixedAsset ? $fixedAsset->specs : null,
            'accountName' => $fixedAsset ? $fixedAsset->account_name : null,
            'department' => $fixedAsset ? $fixedAsset->department : null,
            'dateIssued' => $fixedAsset ? $fixedAsset->date_issued : date('Y-m-d'),
            'action' => $fixedAsset ? $serialNumber . '/update' : 'insert',
            'tags' => $fixedAsset ? $fixedAsset->tags_str : null,
        ]);
    }

    public function insert(Request $request) {
        $rules = [
            'accountName' => 'required',
            'department' => 'required',
            'description' => 'required',
            'serialNumber' => 'required|unique:fixed_assets,id',
            'dateIssued' => 'required',
            'specs' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request) {
                $account = Account::where([
                    'name' => $request->accountName,
                ])->first();

                if($account == null) {
                    // create the account
                    $account = Account::create([
                        'name' => $request->accountName,
                        'department' => $request->department,
                    ]);
                } else {
                    $account->update([
                        'department' => $request->department,
                    ]);
                }

                $fixedAsset = FixedAsset::create([
                    'id' => $request->serialNumber,
                    'description' => $request->description,
                    'specs' => $request->specs,
                    'account_id' => $account->id,
                    'created_at' => $request->dateIssued,
                ]);

                if($request->tags) {
                    $tagIds = collect(explode(",", $request->tags))->transform(function($item) {
                        return trim($item);
                    })->toArray();

                    $existingTags = Tag::whereIn('id', $tagIds)->pluck('id')->toArray();

                    $tagsToInsert = array_diff($tagIds, $existingTags);

                    $tags = collect($tagsToInsert)->transform(function($item) {
                        return [
                            'id' => trim($item)
                        ];
                    })->toArray();

                    Tag::insert($tags);

                    $fixedAsset->tags()->sync($tagIds);
                }


                return redirect(route('scan.fixed-assets', ['serialNumber' => $fixedAsset->id]));
            });
        }
    }

    public function update(Request $request, $serialNumber) {
        $rules = [
            'accountName' => 'required',
            'department' => 'required',
            'description' => 'required',
            'dateIssued' => 'required',
            'specs' => 'required',
        ];

        $fixedAsset = FixedAsset::findOrFail($serialNumber);
        if($fixedAsset->id != $request->serialNumber) {
            $rules['serialNumber'] = 'required|unique:fixed_assets,id';
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $fixedAsset) {
                $account = Account::where([
                    'name' => $request->accountName,
                ])->first();

                if($account == null) {
                    // create the account
                    $account = Account::create([
                        'name' => $request->accountName,
                        'department' => $request->department,
                    ]);
                } else {
                    $account->update([
                        'department' => $request->department,
                    ]);
                }

                $fixedAsset->update([
                    'id' => $request->serialNumber,
                    'description' => $request->description,
                    'specs' => $request->specs,
                    'account_id' => $account->id,
                    'created_at' => $request->dateIssued,
                ]);

                if($request->tags) {
                    $tagIds = collect(explode(",", $request->tags))->transform(function($item) {
                        return trim($item);
                    })->toArray();

                    $existingTags = Tag::whereIn('id', $tagIds)->pluck('id')->toArray();

                    $tagsToInsert = array_diff($tagIds, $existingTags);

                    $tags = collect($tagsToInsert)->transform(function($item) {
                        return [
                            'id' => trim($item)
                        ];
                    })->toArray();

                    Tag::insert($tags);

                    $fixedAsset->tags()->sync($tagIds);
                }

                return redirect(route('scan.fixed-assets', ['serialNumber' => $fixedAsset->id]));
            });
        }
    }

    public function delete($serialNumber) {
        $fixedAsset = FixedAsset::withTrashed()->findOrFail($serialNumber);
        return view('fixed-assets.delete-item', [
            'serialNumber' => $serialNumber,
            'force' => $fixedAsset->deleted_at != null ? 1 : 0,
        ]);
    }

    public function deleteContinue($serialNumber, $force = false) {
        return DB::transaction(function () use ($serialNumber, $force) {
            $fixedAsset = FixedAsset::withTrashed()->findOrFail($serialNumber);

            if($force) {
                $fixedAsset->forceDelete();
            } else {
                $fixedAsset->delete();
            }

            return redirect(route('scan.any', ['code' => $serialNumber]));
        });
    }

    public function addRemarks($serialNumber) {
        $fixedAsset = FixedAsset::withTrashed()->findOrFail($serialNumber);
        return view('fixed-assets.add-remarks', [
            'serialNumber' => $fixedAsset->id,
        ]);
    }

    public function addRemarksContinue(Request $request, $id) {
        $rules = [
            'remarks' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $id) {
                $fixedAsset = FixedAsset::findOrFail($id);

                FixedAssetRemarks::create([
                    'fixed_asset_id' => $fixedAsset->id,
                    'content' => $request->remarks,
                ]);

                return redirect(route('scan.fixed-assets', ['code' => $fixedAsset->id]));
            });
        }
    }
}
