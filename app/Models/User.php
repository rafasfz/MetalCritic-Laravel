<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
Use Str;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $hidden = ['password'];
    protected $keyType = 'string';

    protected static function booted()
    {
        static::creating(fn(User $user) => $user->id = (string) Str::uuid());
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function reviews() {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function games() {
        return $this->morphToMany(Game::class, 'reviews');
    }
}
