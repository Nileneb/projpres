<?php

/**
 * Gibt die CSS-Klassen für einen Match-Status zurück.
 *
 * @param string $status
 * @return string
 */
function match_status_color($status)
{
    return match ($status) {
        'created' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'in_progress' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'submitted' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'closed' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        'incomplete' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    };
}
