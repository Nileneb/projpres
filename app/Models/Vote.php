<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Matches;
use App\Models\User;

class Vote extends Model
{
    use HasFactory;
    protected $fillable = ['match_id', 'user_id', 'score', 'comment'];

    /**
     * Relationship: Vote belongs to a match.
     */
    public function match()
    {
        return $this->belongsTo(Matches::class, 'match_id');
    }

    /**
     * Relationship: Vote belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
