<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\Participant;
use App\Models\Matches as MatchModel;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = \App\Models\User::factory()->count(12)->create();
        // 3 Teams in KW38
        $teams = Team::factory()->count(3)->create(['week_label'=>'2025-KW38']);
        // Teilnehmer verteilen
        foreach ($teams as $i => $team) {
            $slice = $users->slice($i*4, 4);
            foreach ($slice as $u) {
                Participant::create(['team_id'=>$team->id,'user_id'=>$u->id]);
            }
        }
        // Matches: Team1->Team2, Team2->Team3, Team3->Team1
        $pairs = [[0,1],[1,2],[2,0]];
        foreach($pairs as [$c,$s]){
            MatchModel::create([
                'week_label'=>'2025-KW38',
                'creator_team_id'=>$teams[$c]->id,
                'solver_team_id'=>$teams[$s]->id,
                'status'=>'pending',
                'time_limit_minutes'=>20,
            ]);
        }
    }
}
