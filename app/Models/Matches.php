<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Team;
use App\Models\Vote;

class Matches extends Model
{
    use HasFactory;
    protected $table = 'matches';

    protected $fillable = [
        'week_label',
        'creator_id',
        'solver_id',
        'challenge_text',
        'time_limit_minutes',
        'submission_url',
        'started_at',
        'submitted_at',
        'deadline',
        'status'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'started_at' => 'datetime',
        'deadline' => 'datetime'
    ];

    public function creator()
    {
        return $this->belongsTo(Team::class, 'creator_id');
    }

    public function solver()
    {
        return $this->belongsTo(Team::class, 'solver_id');
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
