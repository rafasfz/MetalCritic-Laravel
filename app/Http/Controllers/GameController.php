<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\User;

class GameController extends Controller
{
    private $loggedUser;

    public function __construct() {
        $this->middleware('auth:api', ['except' => ['show', 'index']]);

        $this->loggedUser = auth()->user();
    }

    public function store(Request $request) {
        $name = $request->input('name');
        $publisher = $request->input('publisher');
        $developer = $request->input('developer');
        $release_date = $request->input('release_date');

        if(!($name && $publisher && $developer && $release_date)) {
            return response()->json([
                'message' => 'Please provide name, publisher, developer and release date'
            ], 400);
        }

        if(strtotime($release_date) === false) {
            return response()->json([
                'message' => 'Release date is not valid'
            ], 400);
        }

        $user = User::find($this->loggedUser->id);

        if(!$user->is_admin) {
            return redirect()->route('login');
        }

        $game = Game::where('name', $name)->first();

        if($game) {
            return response()->json([
                'message' => 'Game already exists'
            ], 400);
        }

        $game = new Game();
        $game->name = $name;
        $game->publisher = $publisher;
        $game->developer = $developer;
        $game->release_date = $release_date;

        $game->save();

        return $game;
    }
}
