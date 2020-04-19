<?php

namespace App\Http\Controllers\Web;

use App\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SparePart;
use App\SparePartItem;
use Illuminate\Support\Facades\DB;

class SparePartsController extends Controller
{
    public function show($partNumber) {
        $sparePart = SparePart::withTrashed()
            ->withCount(['sparePartItems as total_items' => function($query) {

            }])
            ->find($partNumber);

        if($sparePart == null) {
            $data = null;
        } else {
            $data = [
                'partNumber' => $sparePart ? $sparePart->id : null,
                'description' => $sparePart ? $sparePart->description : null,
                'specs' => $sparePart ? $sparePart->specs : null,
                'supplier' => $sparePart ? $sparePart->supplier : null,
                'deletedAt' => $sparePart->deleted_at,
                'totalItems' => $sparePart->total_items,
            ];
        }

        return view('spare-parts.profiles.view-item', [
            'data' => $data,
            'partNumber' => $partNumber,
        ]);
    }

    public function addProfile($partNumber = null, $serialNumber = null) {
        $sparePart = SparePart::find($partNumber);

        return view('spare-parts.profiles.create', [
            'partNumber' => $sparePart ? $sparePart->id : $partNumber,
            'description' => $sparePart ? $sparePart->description : null,
            'specs' => $sparePart ? $sparePart->specs : null,
            'supplier' => $sparePart ? $sparePart->supplier : null,
            'action' => $sparePart == null ? 'insert' : $partNumber . '/update/' . $serialNumber,
            'serialNumber' => $serialNumber,
        ]);
    }

    public function insert(Request $request) {
        $rules = [
            'partNumber' => 'required|unique:spare_parts,id',
            'description' => 'required',
            'specs' => 'required',
            'supplier' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request) {
                $sparePart = SparePart::create([
                    'id' => $request->partNumber,
                    'description' => $request->description,
                    'specs' => $request->specs,
                    'supplier' => $request->supplier,
                ]);

                return redirect(route('scan.any', ['code' => $sparePart->id]));
            });
        }
    }

    public function update(Request $request, $partNumber, $serialNumber = null) {
        $rules = [
            'partNumber' => 'required',
            'description' => 'required',
            'specs' => 'required',
            'supplier' => 'required',
        ];

        $sparePart = SparePart::findOrFail($partNumber);

        if($sparePart->id != $request->partNumber) {
            $rules['partNumber'] = 'required|unique:finished_goods,id';
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $sparePart, $serialNumber) {
                $sparePart->update([
                    'id' => $request->partNumber,
                    'description' => $request->description,
                    'specs' => $request->specs,
                    'supplier' => $request->supplier,
                ]);

                return redirect(route('scan.any', ['code' => $serialNumber ? $serialNumber : $sparePart->id]));
            });
        }
    }
}
