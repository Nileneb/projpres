<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Matches;
use App\Models\Team;
use App\Models\User;
use App\Models\Participant;
use App\Services\TeamAssignmentService;
use Illuminate\Support\Facades\DB;

class WeeklyTransition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:weekly-transition {--force : Force transition even if it\'s not the weekend} {--dry-run : Show what would happen without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform weekly transition: close matches, archive teams, and create new teams for the next week';

    /**
     * Execute the console command.
     */
    public function handle(TeamAssignmentService $teamService)
    {
        // Prüfe, ob es Wochenende ist (es sei denn, --force wurde verwendet)
        $isWeekend = in_array(date('N'), [6, 7]); // 6 = Samstag, 7 = Sonntag

        if (!$isWeekend && !$this->option('force')) {
            $this->error('Today is not weekend. Use --force to run anyway.');
            return Command::FAILURE;
        }

        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->info('DRY RUN: No changes will be made to the database.');
        }

        // Aktuelle Woche holen
        $currentWeekLabel = $teamService->getCurrentWeekLabel();
        $this->info("Current week: {$currentWeekLabel}");

        // 1. Alle offenen Matches der aktuellen Woche auf 'closed' setzen
        $openMatches = Matches::where('week_label', $currentWeekLabel)
            ->whereIn('status', ['created', 'in_progress', 'submitted'])
            ->get();

        $this->info("Found {$openMatches->count()} open matches to close.");

        if (!$dryRun && $openMatches->count() > 0) {
            foreach ($openMatches as $match) {
                $match->update(['status' => 'closed']);
                $this->line("Closed match: {$match->id} (Creator: {$match->creator->name}, Solver: {$match->solver->name})");
            }
        }

        // 2. Alle Teams der aktuellen Woche archivieren
        $teams = Team::where('week_label', $currentWeekLabel)->get();
        $this->info("Found {$teams->count()} teams to archive.");

        if (!$dryRun && $teams->count() > 0) {
            foreach ($teams as $team) {
                $team->update(['is_archived' => true]);
                $this->line("Archived team: {$team->name}");
            }
        }

        // 3. Neue Teams für die nächste Woche erstellen (wenn Benutzer vorhanden sind)
        $users = User::where('is_active', true)->get();
        $userCount = $users->count();
        
        $this->info("Found {$userCount} active users for new teams.");

        if ($userCount < 4) {
            $this->warn("Not enough active users to create teams (need at least 4, found {$userCount})");
            return Command::SUCCESS;
        }

        // Nächste Woche berechnen
        $nextWeekDate = strtotime('+1 week');
        $nextWeekYear = date('Y', $nextWeekDate);
        $nextWeekNumber = date('W', $nextWeekDate);
        $nextWeekLabel = "{$nextWeekYear}-KW{$nextWeekNumber}";

        $this->info("Creating teams for next week: {$nextWeekLabel}");

        if (!$dryRun) {
            // Vorherige Teamzusammensetzung laden
            $previousTeams = $this->getPreviousTeamCompositions($currentWeekLabel);

            // Neue Teams erstellen und dabei vorherige Zusammensetzungen vermeiden
            $this->createNewTeams($users, $nextWeekLabel, $previousTeams);
        }

        $this->info('Weekly transition completed successfully.');
        return Command::SUCCESS;
    }

    /**
     * Vorherige Teamzusammensetzungen abrufen
     *
     * @param string $currentWeekLabel
     * @return array
     */
    protected function getPreviousTeamCompositions($currentWeekLabel)
    {
        $compositions = [];

        // Teams der aktuellen Woche laden
        $teams = Team::where('week_label', $currentWeekLabel)->with('users')->get();

        foreach ($teams as $team) {
            $userIds = $team->users->pluck('id')->toArray();
            sort($userIds); // Sortieren für konsistenten Vergleich

            $key = implode('-', $userIds);
            $compositions[$key] = $userIds;
        }

        return $compositions;
    }

    /**
     * Neue Teams erstellen und dabei vorherige Zusammensetzungen vermeiden
     *
     * @param \Illuminate\Database\Eloquent\Collection $users
     * @param string $weekLabel
     * @param array $previousTeams
     * @return void
     */
    protected function createNewTeams($users, $weekLabel, $previousTeams)
    {
        // Benutzer mischen
        $shuffledUsers = $users->shuffle();
        $teamsCount = floor($shuffledUsers->count() / 4);

        $this->info("Creating {$teamsCount} teams with 4 members each");

        $maxAttempts = 50; // Maximale Anzahl von Versuchen, um einzigartige Teams zu erstellen
        $attempts = 0;
        $validTeams = [];

        // Versuche, Teams zu erstellen, die sich von den vorherigen unterscheiden
        while (count($validTeams) < $teamsCount && $attempts < $maxAttempts) {
            $attempts++;

            // Benutzer neu mischen, wenn wir mehrere Versuche brauchen
            if ($attempts > 1) {
                $shuffledUsers = $users->shuffle();
            }

            $validTeams = [];

            // Teams erstellen
            for ($i = 0; $i < $teamsCount; $i++) {
                $teamUsers = $shuffledUsers->slice($i * 4, 4);
                $teamUserIds = $teamUsers->pluck('id')->toArray();
                sort($teamUserIds);

                $key = implode('-', $teamUserIds);

                // Prüfen, ob diese Kombination bereits in einem vorherigen Team existiert
                if (isset($previousTeams[$key])) {
                    $this->line("Attempt {$attempts}: Found duplicate team combination, retrying...");
                    $validTeams = []; // Zurücksetzen und neu versuchen
                    break;
                }

                $validTeams[] = $teamUserIds;
            }
        }

        if (count($validTeams) < $teamsCount) {
            $this->warn("Couldn't create unique team compositions after {$maxAttempts} attempts. Some teams may be similar to previous ones.");

            // Einfach die letzten gemischten Benutzer verwenden
            $validTeams = [];
            for ($i = 0; $i < $teamsCount; $i++) {
                $validTeams[] = $shuffledUsers->slice($i * 4, 4)->pluck('id')->toArray();
            }
        }

        // Teams in der Datenbank erstellen
        DB::beginTransaction();
        try {
            foreach ($validTeams as $index => $teamUserIds) {
                $team = Team::create([
                    'name' => 'Team ' . chr(65 + $index), // A, B, C, ...
                    'week_label' => $weekLabel,
                    'is_archived' => false
                ]);

                // Teilnehmer erstellen
                $skippedUsers = [];
                $teamAssignmentService = app(TeamAssignmentService::class);

                foreach ($teamUserIds as $userId) {
                    $result = $teamAssignmentService->addUserToTeam(
                        $userId,
                        $team->id,
                        $weekLabel,
                        null // Standard-Rolle
                    );

                    if (!$result['success']) {
                        $skippedUsers[] = $userId;
                        $this->warn("User ID {$userId} wurde übersprungen: {$result['message']}");
                    }
                }

                if (count($skippedUsers) > 0) {
                    $this->warn("Es wurden " . count($skippedUsers) . " Benutzer übersprungen beim Erstellen von Team {$team->name}.");
                }

                $this->info("Created team: {$team->name} with " . count($teamUserIds) . " members");
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error creating teams: " . $e->getMessage());
            throw $e;
        }
    }
}
