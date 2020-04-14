<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Que;
use App\QueItem;

class QuesController extends Controller
{
    public function byItems(Request $request) {
        $result = QueItem::with(['que.user' => function($query) {
            $query->select('id', 'lastname', 'firstname');
        }])->where(function($query) use ($request) {
            $query->where('code', 'like', "$request->keyword%")
                ->orWhereHas('que', function($query) use ($request) {
                    $query->where('name', 'like', "%$request->keyword%")
                        ->orWhere('remarks', 'like', "%$request->keyword%");
                });
        })->whereHas('que', function($query) {
            $query->where('completed', true);
        })->where('done', false);

        if($request->queId) {
            $result->where('que_id', $request->queId);
        }

        if($request->userId) {
            $result = $result->where('user_id', $request->userId);
        }

        return response()->json([
            'result' => $result->paginate(30),
        ], 200);
    }

    public function byUsers(Request $request) {
        $result = Que::withCount('ques')
            ->with('user')
            ->where('name', 'like', "%$request->keyword%")
            ->where('completed', true);

        return response()->json([
            'result' => $result->paginate(30)
        ]);
    }
}
