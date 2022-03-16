<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Game;
use Illuminate\Support\Facades\Storage;

class GameImageController extends Controller
{

    private $loggedUser;
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['show', 'index']]);
        $this->loggedUser = auth()->user();
    }

    public function store(Request $request, $id)
    {
        $user = User::find($this->loggedUser->id);

        if(!$user->is_admin) {
            return redirect()->route('login');
        }

        $game = Game::find($id);

        if(!$game) {
            return response()->json([
                'message' => 'Game not found'
            ], 404);
        }

        $image = $request->file('image');
        $image_exist = $request->hasFile('image');

        if(!$image_exist) {
            return response()->json([
                'message' => 'Please provide an image'
            ], 400);
        }

        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/svg'];

        if(!in_array($image->getMimeType(), $allowed_types)) {
            return response()->json([
                'message' => 'Please provide an image with a valid format'
            ], 400);
        }

        if($image->getSize() > 5000000) {
            return response()->json([
                'message' => 'Image size should be less than 5MB'
            ], 400);
        }

        if($game->image) {
            if(Storage::exists('games/' . $game->image)) {
                Storage::delete('games/' . $game->image);
            }
        }

        $image_name = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $image->storeAs('games/', $image_name);
        $game->image = $image_name;
        $game->save();

        return $game;
    }
}
