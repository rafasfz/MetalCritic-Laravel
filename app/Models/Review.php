<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class Review extends Model
{
    protected $keyType = 'string';

    protected static function booted()
    {
        static::creating(fn(Review $review) => $review->id = (string) Str::uuid());
    }

    public function reviewable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
