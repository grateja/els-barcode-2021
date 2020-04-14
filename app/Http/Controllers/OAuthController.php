<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class OAuthController extends Controller
{
    public function login(Request $request) {
        if(auth()->attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            $user = User::findOrFail(auth()->id());

            $token = $user->createToken('csi-2019');
            return response()->json([
                'user' => $user,
                'token' => $token,
                'activeBranch' => $user->activeBranch,
                'retainToken' => $request->rememberMe,
            ]);
        }
        return response()->json([
            'errors' => ['email' => ['Invalid log in credentials']]
        ], 401);
    }

    public function logout() {
        $user = auth('api')->user()->logout();
        return 1;
    }

    public function check() {
        $user = auth('api')->user();
        $token = $user->createToken('csi-2019');

        return response()->json([
            'user' => $user,
            'activeBranch' => $user->activeBranch,
            'token' => $token,
        ], 200);
    }
}
