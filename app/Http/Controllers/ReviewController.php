<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Review;
use App\Models\Game;

class ReviewController extends Controller
{
    private $loggedUser;
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['show', 'index']]);
        $this->loggedUser = auth()->user();
    }

    function store(Request $request) {
        $game_id = $request->input('game_id');
        $user_id = $this->loggedUser->id;
        $score = $request->input('score');
        $comment = $request->input('comment');

        if(!($game_id && $score)) {
            return response()->json([
                'message' => 'Please provide game_id and score'
            ], 400);
        }

        if($score > 100 || $score < 0) {
            return response()->json([
                'message' => 'Score must be between 0 and 100'
            ], 400);
        }

        $user = User::find($user_id);
        $game = Game::find($game_id);

        if(!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        if(!$game) {
            return response()->json([
                'message' => 'Game not found'
            ], 404);
        }

        $review = Review::where('game_id', $game_id)->where('user_id', $user_id)->first();

        if($review) {
            return response()->json([
                'message' => 'You already review this game'
            ], 400);
        }

        $review = new Review();
        $review->game_id = $game_id;
        $review->user_id = $user_id;
        $review->score = $score;
        $review->comment = $comment;

        $review->save();

        $review = Review::where('game_id', $game_id)->where('user_id', $user_id)->first();

        if($game->score === null) {
            $game->score = $score;
        } else {
            $reviews = $game->reviews;
            $gameScore = 0;
            foreach($reviews as $gameReview) {
                $gameScore += $gameReview->score;
            }
            $game->score = $gameScore / count($reviews);

        }

        $game->save();

        return $review;
    }

    function index(Request $request) {
        $game = $request->input('game');
        $user = $request->input('user');
        $limit = $request->input('limit') ? $request->input('limit') : 2;
        $page = $request->input('page') ? $request->input('page') : 1;
        $order_column = $request->input('order_column') ? $request->input('order_column') : 'created_at';
        $order = $request->input('order') === 'desc' ? 'desc' : 'asc';
        $page--;

        if($game) {
            $game = Game::where('id', $game)->first();

            if(!$game) {
                return response()->json([
                    'message' => 'Game not found'
                ], 404);
            }

            $reviews = Review::where('game_id', $game->id)
                ->orderBy($order_column, $order)
                ->offSet($page * $limit)
                ->limit($limit)
                ->get();

            $total_pages = ceil(Review::where('game_id', $game->id)->count() / $limit);
        }
        else if($user) {
            $user = User::where('id', $user)->first();

            if(!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            $reviews = Review::where('user_id', $user->id)
                ->orderBy($order_column, $order)
                ->offSet($page * $limit)
                ->limit($limit)
                ->get();

            $total_pages = ceil(Review::where('user_id', $user->id)->count() / $limit);
        } else {
            $reviews = Review::orderBy($order_column, $order)
                ->offSet($page * $limit)
                ->limit($limit)
                ->get();

            $total_pages = ceil(Review::count() / $limit);
        }

        return ['reviews' => $reviews, 'total_pages' => $total_pages];
    }

    function show($id) {
        $review = Review::find($id);
        $review->game = $review->game;

        if(!$review) {
            return response()->json([
                'message' => 'Review not found'
            ], 404);
        }

        return $review;
    }
}
