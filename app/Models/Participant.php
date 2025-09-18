<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Team;
use App\Models\User;

class Participant extends Model
{
    use HasFactory;
    protected $fillable = ['team_id', 'user_id', 'role'];

    /**
     * Relationship: A participant belongs to a team.
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Relationship: A participant belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
