<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use str;

class Game extends Model
{
    protected $keyType = 'string';

    protected static function booted()
    {
        static::creating(fn(Game $game) => $game->id = (string) str::uuid());
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
