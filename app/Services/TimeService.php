<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonInterface;

class TimeService
{
    /**
     * Get the current time as a Carbon instance
     *
     * @return \Carbon\CarbonInterface
     */
    public function current(): CarbonInterface
    {
        return Carbon::now();
    }

    /**
     * Get the current week label in the format "YYYY-KWnn"
     *
     * @return string
     */
    public function currentWeekLabel(): string
    {
        $now = $this->current();
        $year = $now->isoFormat('GGGG'); // ISO year
        $week = $now->isoWeek();         // ISO week number

        return "{$year}-KW{$week}";
    }

    /**
     * Get the next week label in the format "YYYY-KWnn"
     *
     * @return string
     */
    public function nextWeekLabel(): string
    {
        $nextWeek = $this->current()->addWeek();
        $year = $nextWeek->isoFormat('GGGG'); // ISO year
        $week = $nextWeek->isoWeek();         // ISO week number

        return "{$year}-KW{$week}";
    }

    /**
     * Check if today is a weekend (Saturday or Sunday)
     *
     * @return bool
     */
    public function isWeekend(): bool
    {
        return $this->current()->isWeekend();
    }

    /**
     * Format a date in a human-readable format
     *
     * @param \Carbon\CarbonInterface|string|null $date
     * @param string $format Default format for dates
     * @return string
     */
    public function formatDate($date = null, string $format = 'd.m.Y H:i'): string
    {
        if (is_null($date)) {
            $date = $this->current();
        } elseif (is_string($date)) {
            $date = Carbon::parse($date);
        }

        return $date->format($format);
    }

    /**
     * Calculate deadline from a start time
     *
     * @param \Carbon\CarbonInterface|null $startTime Start time (defaults to now)
     * @param int $minutes Minutes to add for the deadline
     * @return \Carbon\CarbonInterface
     */
    public function calculateDeadline(?CarbonInterface $startTime = null, int $minutes = 20): CarbonInterface
    {
        $startTime = $startTime ?? $this->current();
        return $startTime->copy()->addMinutes($minutes);
    }
}
