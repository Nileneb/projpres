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

        // Pagination für verfügbare Wochen (10 pro Seite)
        $perPage = 10;
        $page = $request->input('page', 1);
        $totalWeeks = count($availableWeeks);
        $paginatedWeeks = array_slice($availableWeeks, ($page - 1) * $perPage, $perPage);
        $hasMorePages = $totalWeeks > $page * $perPage;
        $hasPreviousPages = $page > 1;

        // Ausgewählte Woche (Standard: neueste verfügbare Woche aus der aktuellen Paginationsseite)
        $selectedWeek = $request->input('week', $paginatedWeeks[0] ?? null);

        // Wenn keine historische Woche verfügbar ist
        if (empty($selectedWeek)) {
            return view('history.index', [
                'currentWeek' => $currentWeek,
                'availableWeeks' => [],
                'paginatedWeeks' => [],
                'selectedWeek' => null,
                'teams' => collect(),
                'matches' => collect(),
                'page' => 1,
                'hasMorePages' => false,
                'hasPreviousPages' => false,
                'totalWeeks' => 0,
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

        return view('history.index', compact(
            'currentWeek',
            'availableWeeks',
            'paginatedWeeks',
            'selectedWeek',
            'teams',
            'matches',
            'page',
            'hasMorePages',
            'hasPreviousPages',
            'totalWeeks'
        ));
    }
}
