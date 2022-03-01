<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Game;

class UserController extends Controller
{

    private $loggedUser;

    public function __construct() {
        $this->middleware('auth:api', ['except' => ['store', 'show']]);

        $this->loggedUser = auth()->user();
    }

    public function store(Request $request) {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $password_confirm = $request->input('password_confirm');

        if(!($name && $email && $password)) {
            return response()->json([
                'message' => 'Please provide name, email and password'
            ], 400);
        }

        $emailExists = User::where('email', $email)->count();

        if($emailExists) {
            return response()->json([
                'message' => 'Email already registred'
            ], 400);
        }

        if($password !== $password_confirm) {
            return response()->json([
                'message' => 'Passwords do not match'
            ], 400);
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = $hash;
        $user->save();
        $user = User::where('email', $email)->first();


        return $user;
    }

    public function show($id) {
        $user = User::find($id);

        if(!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $user->reviews = $user->reviews;

        foreach($user->reviews as $review) {
            $review->game = $review->game;
        }

        return $user;
    }

    public function update(Request $request, $id) {
        if($this->loggedUser->id !== $id) {
            return response()->json([
                'message' => 'You are not allowed to update this user'
            ], 403);
        }

        $user = User::find($id);

        if(!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $password_confirm = $request->input('password_confirm');
        $new_password = $request->input('old_password');

        if(!$password) {
            return response()->json([
                'message' => 'Please provide password'
            ], 400);
        }

        if(!Auth::attempt(['email' => $user->email, 'password' => $password])) {
            return response()->json([
                'message' => 'Wrong password'
            ], 400);
        }

        if($name) {
            $user->name = $name;
        }

        if($email) {
            $user->email = $email;
        }

        if($new_password) {
            if($new_password !== $password_confirm) {
                return response()->json([
                    'message' => 'Passwords do not match'
                ], 400);
            }

            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            $user->password = $hash;
        }

        $user->save();

        return $user;

    }
}
