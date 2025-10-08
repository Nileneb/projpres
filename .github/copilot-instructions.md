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

### Project-Specific Patterns
- Week labels use format "YYYY-KWnn" (e.g., "2023-KW38")
- Teams can only create one challenge per week
- Teams can only receive one challenge per week
- Team members must be assigned via the `participants` table

### Important Implementation Details
1. **Voting Authorization**: Voting is authorized through the policy system using `Gate::authorize('create', [Vote::class, $match])` which ensures only users who are not on the creator or solver teams can vote, and only when the match status is 'submitted'.
2. **Match Creation Flow**: Creating a match works in two steps - first showing available teams if no parameters provided, then showing the creation form with the selected team.

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
