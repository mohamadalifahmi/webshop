<?php

namespace App\Console;

use App\Jobs\CancelUnshippedOrders;
use App\Jobs\ReleaseHeldEarnings;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [];

    protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule): void
    {
        $schedule->command('inspire')->hourly();

        $schedule->job(new ReleaseHeldEarnings())
            ->daily()
            ->withoutOverlapping();

        $schedule->job(new CancelUnshippedOrders())
            ->daily()
            ->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/../routes/console.php');
    }
}