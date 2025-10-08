<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Participant;
use App\Models\User;
use App\Models\Matches as MatchModel;

class Team extends Model
{
    use HasFactory;
    protected $fillable = ['week_label', 'name', 'is_archived'];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_archived' => 'boolean',
    ];

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
    
    /**
     * Scope a query to only include active (non-archived) teams.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('is_archived', false);
    }
    
    /**
     * Scope a query to only include archived teams.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeArchived(Builder $query)
    {
        return $query->where('is_archived', true);
    }
    
    /**
     * Archive this team.
     *
     * @return bool
     */
    public function archive()
    {
        $this->is_archived = true;
        return $this->save();
    }
    
    /**
     * Unarchive this team.
     *
     * @return bool
     */
    public function unarchive()
    {
        $this->is_archived = false;
        return $this->save();
    }
}
