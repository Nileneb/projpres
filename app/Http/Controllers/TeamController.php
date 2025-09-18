<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller {
    public function index(){
        // Alle Teams gruppiert nach week_label holen
        $teams = Team::all();
        $weekLabels = $teams->pluck('week_label')->unique()->sort()->values()->all();

        // Teams nach Woche gruppieren
        $teamsByWeek = [];
        foreach ($weekLabels as $weekLabel) {
            $teamsByWeek[$weekLabel] = $teams->where('week_label', $weekLabel)->all();
        }

        return view('teams.index', compact('weekLabels', 'teamsByWeek'));
    }
}
