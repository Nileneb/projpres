# Weekly Team Challenge Platform - Scheduler Configuration

## Timezone Settings

The weekly transition scheduler is now configured to use the Europe/Berlin timezone. This ensures that the weekly transition task (which runs every Sunday at midnight) correctly triggers based on the local time in Berlin.

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
- In production, this should be configured as a cron job as per Laravel documentation
- The timezone setting only applies to when the task runs, not to the PHP application timezone
