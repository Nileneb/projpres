<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Team;
use App\Models\Vote;

class Matches extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'week_label',
        'creator_team_id',
        'solver_team_id',
        'challenge_text',
        'time_limit_minutes',
        'submission_url',
        'submitted_at',
        'status'
    ];

    protected $casts = [
        'submitted_at' => 'datetime'
    ];

    public function creator()
    {
        return $this->belongsTo(Team::class, 'creator_team_id');
    }

    public function solver()
    {
        return $this->belongsTo(Team::class, 'solver_team_id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class, 'match_id');
    }

    public function avgScore()
    {
        return $this->votes()->avg('score');
    }
}
