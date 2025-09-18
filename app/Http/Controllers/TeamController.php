<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller {
    public function index(){
        $teams = Team::query()->latest()->paginate(20);
        return view('teams.index', compact('teams'));
    }
}
