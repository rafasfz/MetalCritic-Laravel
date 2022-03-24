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

    private function getManyGames($page, $limit, $orderProperty, $order) {
        $games = Game::orderBy($orderProperty, $order)
                ->offSet($page * $limit)
                ->limit($limit)
                ->get();
        $total = Game::count();
        $total_pages = ceil($total / $limit);

        return [
            'games' => $games,
            'total_pages' => $total_pages
        ];
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

    public function index(Request $request) {
        $page = $request->input('page') ? $request->input('page') : 1;
        $limit = $request->input('limit') ? $request->input('limit') : 2;
        $order = $request->input('order') ? $request->input('order') : 'asc';
        $order = $order === 'desc' ? 'desc' : 'asc';
        $orderBy = $request->input('order') ? $request->input('order') : null;
        $orderBy = $orderBy === 'name' || $order === 'score' ? $order : 'release_date';
        $page--;

        ['games' => $games, 'total_pages' => $total_pages] = $this->getManyGames($page, $limit, $orderBy, $order);
        $next_page_url = $page + 1 < $total_pages
            ? url('/api/games?page=' . ($page + 2) . '&limit=' . $limit . '&order=' . $order)
            : null;

        return response()->json([
            'games' => $games,
            'total_pages' => $total_pages,
            'next_page_url' => $next_page_url
        ]);
    }

    public function show(Request $request, $id) {
        $page = $request->input('page') ? $request->input('page') : 1;
        $limit = $request->input('limit') ? $request->input('limit') : 2;
        $page--;

        $game = Game::with(['reviews' => function($query) use ($limit, $page) {
            $query->orderBy('created_at', 'desc')
                ->offSet($page * $limit)
                ->limit($limit);
        }])->find($id);

        if(!$game) {
            return response()->json([
                'message' => 'Game not found'
            ], 404);
        }

        return $game;
    }

    public function update(Request $request, $id) {
        $game = Game::find($id);

        if(!$game) {
            return response()->json([
                'message' => 'Game not found'
            ], 404);
        }

        $name = $request->input('name');
        $publisher = $request->input('publisher');
        $developer = $request->input('developer');
        $release_date = $request->input('release_date');

        $user = User::find($this->loggedUser->id);

        if(!$user->is_admin) {
            return redirect()->route('login');
        }

        if($name) {
            $game->name = $name;
        }

        if($publisher) {
            $game->publisher = $publisher;
        }

        if($developer) {
            $game->developer = $developer;
        }

        if($release_date) {
            if(strtotime($release_date) === false) {
                return response()->json([
                    'message' => 'Release date is not valid'
                ], 400);
            }

            $game->release_date = $release_date;
        }

        $game->save();

        return $game;
    }

    public function delete($id) {
        $game = Game::find($id);

        if(!$game) {
            return response()->json([
                'message' => 'Game not found'
            ], 404);
        }

        $user = User::find($this->loggedUser->id);

        if(!$user->is_admin) {
            return redirect()->route('login');
        }

        $game->delete();

        return response()->json([
            'message' => 'Game deleted'
        ], 200);
    }
}
