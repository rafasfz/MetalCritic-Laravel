<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenController extends Controller
{

    public function store(Request $request) {
        $email = $request->input('email');
        $password = $request->input('password');

        if(!($email && $password)) {
            return response()->json([
                'message' => 'Please provide email and password'
            ], 400);
        }

        $token = Auth::attempt([
            'email' => $email,
            'password' => $password
        ]);

        if(!$token) {
            return response()->json([
                'message' => 'Email or password is incorrect'
            ], 401);
        }

        return response()->json([
            'token' => $token
        ]);
    }

}
