# Weekly Mini-Game Platform

A Laravel 12 + Livewire application for student team challenges.  
Built as part of the *Grundlagen Programmieren – Datenbank Modell* assignment.

---

## 🎯 Concept

- Every **week** (`week_label`) students are assigned randomly to **teams** (4 members each).
- Each team **creates one challenge** and **solves another team's challenge** within **20 minutes**.
- **Submissions** (e.g. link, file URL) are uploaded by the solver team.
- Afterwards, all **other users** can **vote (1–5)** and leave a comment.
- **Scoring rule**: creator and solver teams both receive the same points based on votes.

---

## 🗄 Database Schema (5 Models incl. `User`)

- **users** – authentication (Breeze + Livewire).
- **teams** – groups of 4 per `week_label`.
- **participants** – link table (User ↔ Team), treated as model (`role`, timestamps).
- **matches** – one challenge per creator/solver team pair; tracks challenge text, submission, status.
- **votes** – 1 vote per user per match (excluding creator/solver teams).

---

## 🚀 Development Setup

### Installation

```bash
# Clone repository
git clone https://github.com/yourusername/challenge-roulette.git
cd challenge-roulette

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class=TestDataSeeder

# Build frontend assets
npm run build

# Start development server
php artisan serve
```

### Testing

```bash
# Run tests
php artisan test
```

### Weekly Scheduling

The application includes a scheduled command to manage the weekly challenge cycle:

```bash
# Run the weekly transition command manually (requires admin rights)
php artisan app:weekly-transition

# Options:
php artisan app:weekly-transition --force   # Skip confirmation prompt
php artisan app:weekly-transition --dry-run # Preview changes without applying

# Run the scheduler locally (for development)
php artisan schedule:work
```

The weekly command performs these actions:
- Closes all open matches from the current week
- Archives current teams
- Prepares for the next week

This task is scheduled to run every Sunday at midnight (Europe/Berlin timezone).

### Seeding Test Data

```bash
# Generate test users, teams, matches and votes
php artisan db:seed --class=TestDataSeeder

# Re-run seeder (if data already exists)
php artisan db:seed --class=TestDataSeeder --force

# Generate only users
php artisan db:seed --class=UserSeeder
```

---

## 🔍 Key Features

### Match Status Flow

Matches follow this status flow:
```
created → in_progress → submitted → closed
```

### Team Participation Settings

Users can opt in/out of participation for the next week via their profile settings (`is_active` field).

### Team Generation

Team generation only includes active users. The system requires at least 4 active users to create meaningful teams (minimum 2 teams with 2 members each).

### Admin Rights

Admin users have additional permissions controlled via the `manage-teams` Gate defined in `AuthServiceProvider`:

```php
// AuthServiceProvider.php
Gate::define('manage-teams', function (User $user) {
    return $user->is_admin === true;
});
```

Admin users can:
- Generate teams for new weeks
- Archive teams (individually or by week)
- Access team management features
- Run the weekly transition manually

### Archive Functionality

#### Archiving Teams
Teams can be archived by admins to maintain a clean workspace:
- Archive individual teams from the Teams page
- Archive all teams from a specific week

#### Archive Filters
Both the Leaderboard and History views include archive filters:
- Toggle "Show archived teams" to include/exclude archived content
- History view shows visual indicators (amber border) for archived teams
- Leaderboard can filter scores from archived teams

---

## 🔧 Common Issues and Solutions

### Cookie Security Issues

If you encounter errors like "Cookie was rejected because it has the 'secure' attribute but was transmitted over HTTP", add these to your `.env`:

```
SESSION_SECURE_COOKIE=false
SESSION_DOMAIN=null
SESSION_SAME_SITE=lax
```

### Team Generation Issues

- Ensure there are enough active users (at least 4)
- If trying to regenerate teams, use the "Bestehende Teams überschreiben" option
- Check user's `is_active` status in settings if they're not appearing in teams

### Time Handling

The application uses a centralized `TimeService` for all time-related operations:

```php
// Dependency injection in controllers/services
public function myMethod(TimeService $timeService) {
    $now = $timeService->current();
    $weekLabel = $timeService->currentWeekLabel();
}
```

Key features:
- Timezone standardization (Europe/Berlin)
- Week labels in ISO format (YYYY-KWnn)
- Deadline calculation for challenges
- Weekend detection for scheduled transitions

Time settings are configured in multiple places:
- `.env`: `APP_TIMEZONE=Europe/Berlin`
- `config/app.php`: `'timezone' => env('APP_TIMEZONE', 'UTC')`
- `app/Console/Kernel.php`: `protected $timezone = 'Europe/Berlin'`
- `phpunit.xml`: `<env name="APP_TIMEZONE" value="Europe/Berlin"/>`
