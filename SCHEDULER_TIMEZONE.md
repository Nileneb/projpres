# Weekly Team Challenge Platform - Scheduler Configuration

## Timezone Settings

The weekly transition scheduler is now configured to use the Europe/Berlin timezone. This ensures that the weekly transition task (which runs every Sunday at midnight) correctly triggers based on the local time in Berlin.

### Comprehensive Timezone Configuration

The application uses Europe/Berlin as its standard timezone, configured in multiple places:

1. **Application Environment**: 
   ```
   # In .env file
   APP_TIMEZONE=Europe/Berlin
   ```

2. **Application Config**:
   ```php
   // In config/app.php
   'timezone' => env('APP_TIMEZONE', 'UTC'),
   ```

3. **Testing Environment**:
   ```xml
   <!-- In phpunit.xml -->
   <env name="APP_TIMEZONE" value="Europe/Berlin"/>
   ```

4. **Scheduler Timezone**: See below for scheduler-specific configuration

## Configuration Details

The scheduler timezone has been set in the `app/Console/Kernel.php` file:

```php
protected function schedule(Schedule $schedule): void
{
    // Wöchentliche Transition jeden Sonntag um Mitternacht ausführen (Europe/Berlin Timezone)
    $schedule->command('app:weekly-transition')
        ->sundays()
        ->at('00:00')
        ->timezone('Europe/Berlin');
}
```

This configuration ensures that:

1. The weekly transition command runs at midnight CET/CEST (Central European Time)
2. All date calculations within the command respect the specified timezone
3. Logs and timestamps accurately reflect the Berlin timezone

## Testing the Scheduler

To test the scheduler configuration:

1. List all scheduled tasks (note: may require running the task once to register in the scheduler):
   ```
   php artisan schedule:list
   ```

2. Run the task manually with the dry-run option:
   ```
   php artisan app:weekly-transition --dry-run
   ```

3. For local development, you can run the scheduler in the foreground:
   ```
   php artisan schedule:work
   ```

## Important Notes

- The scheduler worker must be running for automated transitions to occur
- In production, this should be configured as a cron job as per Laravel documentation:
  ```bash
  * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
  ```
- Both the scheduler timezone and the APP_TIMEZONE configuration are important:
  - The scheduler timezone controls when tasks run
  - The APP_TIMEZONE affects how dates and times are handled within application code
  - These should typically be set to the same value to avoid confusion

## TimeService Integration

The application uses a centralized `TimeService` for all time-related operations, which respects the configured timezone:

```php
// Example usage in controllers:
public function someAction(TimeService $timeService) {
    $now = $timeService->current(); // Returns Carbon instance in APP_TIMEZONE
    $currentWeek = $timeService->currentWeekLabel(); // Returns "YYYY-KWnn" format
}
```

This service handles week transitions, deadlines, and ensures consistent timezone handling throughout the application.
