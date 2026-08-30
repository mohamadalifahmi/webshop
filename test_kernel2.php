<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
echo "Kernel class: " . get_class($kernel) . "\n";

// Check if it's our Kernel
if ($kernel instanceof \App\Console\Kernel) {
    echo "It IS our App\Console\Kernel!\n";
} else {
    echo "It is NOT our App\Console\Kernel - it's " . get_class($kernel) . "\n";
}

// Try to call schedule
$schedule = new \Illuminate\Console\Scheduling\Schedule(new \Illuminate\Filesystem\Filesystem());
$kernel->schedule($schedule);
echo "\nScheduled events:\n";
foreach ($schedule->events() as $event) {
    echo "  - " . $event->summaryForDisplay() . "\n";
}
