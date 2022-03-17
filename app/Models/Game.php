<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Str;

class Game extends Model
{
    protected $keyType = 'string';

    protected static function booted()
    {
        static::creating(fn(Game $game) => $game->id = (string) str::uuid());

        static::retrieved(fn(Game $game) =>
            $game->imageURL
            = $game->image !== null
            ? url('/api/games/' . $game->id . '/images/')
            : null);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
