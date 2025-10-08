# Copilot Instructions for Weekly Mini-Game Platform

## Project Overview
A Laravel 12 + Livewire application for student team challenges where teams are randomly assigned weekly, create and solve challenges, and vote on submissions.

## Core Architecture

### Models & Relationships
- `User` - Auth model with relationships to `Participant`, `Team` (via participants), and `Vote`
- `Team` - Contains `week_label` and `name`, has many `Participant`s, belongs to many `User`s
- `Participant` - Join model between `User` and `Team` with optional `role` field
- `Matches` - Challenge model with `creator_team_id`, `solver_team_id`, `challenge_text`, and tracking fields
- `Vote` - Records user votes (1-5) and comments on match submissions

### Key Service
- `TeamAssignmentService` - Manages team assignments, checks if teams have created/received challenges

### Authorization
- Role-based via `MatchPolicy` and `VotePolicy`
- Admin-specific permissions via `Gate::define('manage-teams')` in `AuthServiceProvider`

## Workflows & Conventions

### Weekly Challenge Cycle
1. Admin assigns users to teams via `TeamAssignmentController`
2. Teams create challenges via `MatchController@create`
3. Solver teams start challenges via `MatchController@start` (20-min time limit)
4. Teams submit solutions via `MatchController@submitSolution`
5. Other users vote on solutions (1-5 scale)

### Status Flow for Matches
The match status follows this flow:
```
created -> in_progress -> submitted -> closed
```

Each status represents a different stage in the challenge lifecycle:
- **created**: Initial state when a challenge is first created
- **in_progress**: When a solver team starts working on the challenge
- **submitted**: When the solver team has submitted their solution
- **closed**: When voting period has ended (managed by admin)

Note: Originally, the database used 'pending' as the default, but migrations have been added to standardize on 'created' as the initial state. The codebase has been updated to use these standardized status values everywhere.

### Project-Specific Patterns
- Week labels use format "YYYY-KWnn" (e.g., "2023-KW38")
- Teams can only create one challenge per week
- Teams can only receive one challenge per week
- Team members must be assigned via the `participants` table

### Important Implementation Details
1. **Voting Authorization**: Voting is authorized through the policy system using `Gate::authorize('create', [Vote::class, $match])` which ensures only users who are not on the creator or solver teams can vote, and only when the match status is 'submitted'.
2. **Match Creation Flow**: Creating a match works in two steps:
   - First step: `MatchController@create` without parameters shows the `matches.select_team` view with available teams
   - Second step: After selecting a team, the `matches.create` form is shown with the selected team's information
   - This two-step process ensures users can only challenge teams that don't already have a challenge

## Key Integration Points
- `MatchController` uses `TeamAssignmentService` for challenge validation
- Voting restricted based on team membership (can't vote on your own team's challenge)
- Admin-only functionality for generating teams

## Development Commands
```bash
# Setup environment
php artisan migrate
php artisan db:seed --class=TestDataSeeder

# Development server
php artisan serve

# Run tests
php artisan test
```

## Common Issues and Solutions

### Cookie Security Issues
If you encounter errors like "Cookie was rejected because it has the 'secure' attribute but was transmitted over HTTP", add these to your `.env`:
```
SESSION_SECURE_COOKIE=false
SESSION_DOMAIN=null
SESSION_SAME_SITE=lax
```

## Core Files
- `app/Services/TeamAssignmentService.php` - Team management logic
- `app/Http/Controllers/MatchController.php` - Challenge workflow
- `app/Policies/MatchPolicy.php` - Permission rules for challenges
- `database/migrations/2023_09_18_*.php` - Core schema definitions

## SQL Queries for Reports

### User Points Leaderboard
```sql
SELECT u.id, u.name,
       COALESCE(SUM(v.score),0) AS total_points
FROM users u
LEFT JOIN participants p ON p.user_id = u.id
LEFT JOIN teams t ON t.id = p.team_id
LEFT JOIN matches m ON m.week_label = t.week_label
   AND (m.creator_team_id = t.id OR m.solver_team_id = t.id)
LEFT JOIN votes v ON v.match_id = m.id
GROUP BY u.id, u.name
ORDER BY total_points DESC;
```
# Challenge Roulette – Dev To‑Do Board

## ✅ Done

* Individuelles Ranking (User-Score über alle Wochen); Leaderboard-Route & View vorhanden.
* Status-Flow konsistent: `created → in_progress → submitted → closed`; Vote-Policy erlaubt Voten erst bei `submitted`.
* Participants als eigenes Modell, Relationen zu User/Team vorhanden.
* Team-Generierung & Match-Erzeugung per Service/Controller; Matches werden persistent angelegt.

---

## ⛏️ To‑Do (Next Up)

1. **Voting-Request vereinheitlichen**

   * Formularfeld auf `name="score"` umstellen (statt `rating`) **oder** `VoteController@store(StoreVoteRequest $request, Matches $match)` verwenden und Request-Regeln auf `score` ausrichten.
   * Ziel: einheitliche Benennung in View, Request, Controller, DB.

2. **FormRequests konsequent nutzen**

   * `MatchController@store` → `CreateChallengeRequest` injizieren, Inline-Validation entfernen.
   * `MatchController@submitSolution` → `SubmitMatchRequest` injizieren, Inline-Validation entfernen.
   * `VoteController@store` → `StoreVoteRequest` injizieren.

3. **Dashboard/Listen auf aktuelle Woche filtern**

   * `TeamAssignmentService::getCurrentWeekLabel()` verwenden.
   * `MatchController@index` & Dashboard-Queries: `where('week_label', currentWeek)`.

4. **Wochenwechsel / History**

   * Command/Job: Am Wochenende `status='closed'` setzen (und optional `archived`).
   * Dashboard zeigt nur `currentWeek` + `status != archived`.
   * Separate History-Ansicht: vergangene `week_label` read-only listen.
   >> direkt randomize Teams für neue Woche (identische Teams wie letzte Woche vermeiden!>> Wie kann man das klug umsetzen?)

5. **Roulette-Animation integrieren**

   * `resources/js/app.js`: Canvas/SVG-Animation (Spin, Hover, Click → Segment-Details).
   * Optional GSAP/Easing, Livewire-Event bei Selection.

6. **Guardrail: 1 Team pro User je Woche**

   * In `TeamAssignmentService`: vor Insert checken, ob User schon in `participants` für `week_label` hängt; sonst überspringen/loggen.

7. **Tests (Policies/Flows)**

   * Vote-Policy: Creator-/Solver-Mitglieder dürfen nicht voten.
   * Match-Submit: nur Solver + Status `in_progress`.
   * Leaderboard: Punkte-Aggregation korrekt (Creator & Solver bekommen gleich viele Punkte).

8. **UI-Badges vereinheitlichen**

   * Eine Status-Menge final: `created, in_progress, submitted, closed`.
   * Einheitliche Badge-Komponente (Tailwind/Blade Partial) und überall verwenden.

---

## 📝 Nice to Have / Later

* Archiv-Export (CSV) pro Woche.
* Submissions als eigene Tabelle (mehrere Artefakte + Metadaten).
* Leaderboard: Tie-Breaker (Anzahl Votes), Pagination.
* Admin-Panel für Week-Roll & Bulk-Assignments.


Next-Todo:
# Challenge Roulette – Dev To‑Do Board (v4)

## ✅ Done

* [x] **Status-Vokabular normalisiert**: Migrations gleichen alte Werte an und setzen Default auf `created`.
* [x] **Weekly Transition & Scheduling**: Konsolenbefehl schließt offene Matches, archiviert Teams und bereitet die nächste Woche vor; Task sonntags terminiert.
* [x] **FormRequests verdrahtet**: `CreateChallengeRequest`, `SubmitMatchRequest`, `StoreVoteRequest`, `GenerateTeamRequest`, `ArchiveTeamRequest` – Controller greifen darauf zu.
* [x] **Voting vereinheitlicht**: Formular/Request/Controller nutzen `score`, Policy prüft Teilnahme & Status.
* [x] **Start setzt Zeitfenster**: `started_at` und `deadline` werden beim Starten der Challenge gesetzt.
* [x] **History & Leaderboard**: History-Ansicht für vergangene Wochen; Leaderboard aggregiert Punkte auf User-Ebene (All‑Time & Current‑Week, optional ohne archivierte Teams).
* [x] **Policies & Gates**: `AuthServiceProvider` registriert Policies; Gate `manage-teams` (Admin) aktiv.
* [x] **Team‑Archivierung**: Feld `is_archived` + Scopes/Controller‑Aktionen.
* [x] **1 Team / User / Woche**: Service‑Check verhindert Doppelzuweisung.

---

## ⛏️ To‑Do (Next Up)

1. **Deadline erzwingen**

   * Bei **Submit**: `abort` wenn `now() > deadline`; freundliche Fehlermeldung im UI.
   * Tests: rechtzeitig vs. zu spät.

2. **Dashboard‑CTAs korrigieren**

   * Für Solver:

     * Status `created` → **Start**‑Button (Route `matches.start`).
     * Status `in_progress` → **Submit**‑Button.
   * Submit‑Form nur bei `in_progress` verlinken.

3. **Status‑Badges vereinheitlichen**

   * Überall `<x-match-status :status="..."/>` verwenden.
   * Alte CSS‑Klassen (`status-pending` etc.) entfernen/umbennen (`created`, `in_progress`, `submitted`, `closed`).

4. **Settings: „Teilnahme nächste Woche“**

   * Checkbox in Profile‑Settings → `users.is_active`.
   * `SettingsController@updateProfile`: `is_active` validieren/speichern.
   * Tests für Opt‑in/Opt‑out.

5. **Team‑Generierung auf aktive Nutzer beschränken**

   * In `TeamAssignmentController@generate` `User::all()` → `User::where('is_active', true)->get()`.
   * Hinweis im UI, wenn zu wenige aktive Nutzer vorhanden sind.

6. **Docs/Dev‑UX**

   * README ergänzen: Weekly‑Command (`--force`, `--dry-run`), Seeding/Factories, lokales Scheduling (`schedule:work`).

7. **Roulette‑Animation**

   * `resources/js/app.js`: Canvas/SVG‑Wheel (Spin, Ease, Event‑Emit nach Auswahl), Livewire‑Hook.

8. **Scheduler‑Zeitzone**

   * App/Kernel auf `Europe/Berlin` ausrichten oder `->timezone('Europe/Berlin')` setzen.

9. **Tests erweitern**

   * Start/Submit‑Gating, Deadline, Settings‑Toggle, Team‑Generierung nur aktive Nutzer.

---

## 🧹 Cleanups

* Doppelte Settings‑Views zusammenführen (`resources/views/settings`, `pages/settings`, `livewire/settings`).
* Inline‑Status‑Spans im Dashboard durch Komponente ersetzen.
* Nicht genutzte CSS‑Reste entfernen; kleine UI‑Tooltips für Buttons.


