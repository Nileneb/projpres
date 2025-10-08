@php
$statusClasses = [
    'created' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
    'in_progress' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
    'submitted' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
    'closed' => 'bg-gray-100 text-gray-800 dark:bg-gray-700/30 dark:text-gray-300',
];

$displayText = [
    'created' => 'Created',
    'in_progress' => 'In Progress',
    'submitted' => 'Submitted',
    'closed' => 'Closed',
];

$class = $statusClasses[$status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700/30 dark:text-gray-300';
$text = $displayText[$status] ?? ucfirst($status);
@endphp

<span {{ $attributes->merge(['class' => 'px-2.5 py-0.5 text-xs font-medium rounded-full inline-flex items-center ' . $class]) }}>
    {{ $text }}
</span>
