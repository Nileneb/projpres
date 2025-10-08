<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\Matches;
use App\Services\TeamAssignmentService;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    /**
     * Zeigt die History-Ansicht mit Daten aus vergangenen Wochen.
     */
    public function index(Request $request, TeamAssignmentService $teamService)
    {
        // Aktuelle Woche holen
        $currentWeek = $teamService->getCurrentWeekLabel();
        
        // Verfügbare Wochen-Labels aus der Datenbank holen (außer der aktuellen)
        $availableWeeks = Team::select('week_label')
            ->where('week_label', '!=', $currentWeek)
            ->distinct()
            ->orderBy('week_label', 'desc')
            ->pluck('week_label')
            ->toArray();
            
        // Ausgewählte Woche (Standard: neueste verfügbare Woche)
        $selectedWeek = $request->input('week', $availableWeeks[0] ?? null);
        
        // Wenn keine historische Woche verfügbar ist
        if (empty($selectedWeek)) {
            return view('history.index', [
                'currentWeek' => $currentWeek,
                'availableWeeks' => [],
                'selectedWeek' => null,
                'teams' => collect(),
                'matches' => collect(),
            ]);
        }
        
        // Teams für die ausgewählte Woche laden
        $teams = Team::where('week_label', $selectedWeek)
            ->with('users')
            ->orderBy('name')
            ->get();
            
        // Matches für die ausgewählte Woche laden
        $matches = Matches::where('week_label', $selectedWeek)
            ->with(['creator', 'solver', 'votes'])
            ->get();
            
        return view('history.index', compact('currentWeek', 'availableWeeks', 'selectedWeek', 'teams', 'matches'));
    }
}
