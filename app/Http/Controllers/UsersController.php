<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use DB;

class UsersController extends Controller
{
    public function index(Request $request) {
        $users = User::where(function($query) use ($request) {
            $query->where('name', 'like', "%$request->keyword%");
        })->whereHas('roles', function($query) {
            $query->where('name', 'staff');
        });

        return response()->json([
            'result' => $users->get()
        ], 200);
    }

    public function autocomplete(Request $request) {
        $data = User::where(function($query) use ($request) {
            $query->where('name', 'like', "%$request->keyword%");
        })->limit(10)->get();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function create(Request $request) {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:4|confirmed',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'contact_number' => $request->contactNumber,
                    'password' => bcrypt($request->password),
                ]);

                $user->assignRole(3);

                return response()->json([
                    'user' => $user
                ], 200);
            });
        }
    }

    public function update($userId, Request $request) {
        $rules = [
            'name' => 'required',
        ];

        $user = User::findOrFail($userId);
        if($user->email != $request->email) {
            $rules['email'] = 'required|email|unique:users';
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($user, $request) {
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'contact_number' => $request->contactNumber,
                ]);

                return response()->json([
                    'user' => $user,
                ]);
            });
        }
    }

    public function changePassword($userId, Request $request) {
        $rules = [
            'password' => 'required|min:4|confirmed',
        ];

        if($request->validate($rules)) {
            $user = User::findOrFail($userId);

            return DB::transaction(function () use ($user, $request) {
                $user->update([
                    'password' => bcrypt($request->password),
                ]);

                return response()->json([
                    'user' => $user,
                ]);
            });
        }
    }

    public function deleteUser($userId) {
        $user = User::findOrfail($userId);
        if($user->delete()) {
            return response()->json([
                'user' => $user,
            ]);
        }
    }
}
