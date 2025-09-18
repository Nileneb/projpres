<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Participant;
use App\Models\User;
use App\Models\MatchModel;

class Team extends Model
{
    protected $fillable = ['week_label', 'name'];

    /**
     * Relationship: A team has many participants.
     */
    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    /**
     * Relationship: A team belongs to many users via participants.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'participants');
    }

    /**
     * Relationship: A team has many created matches.
     */
    public function createdMatches()
    {
        return $this->hasMany(MatchModel::class, 'creator_team_id');
    }

    /**
     * Relationship: A team has many solved matches.
     */
    public function solvedMatches()
    {
        return $this->hasMany(MatchModel::class, 'solver_team_id');
    }
}
