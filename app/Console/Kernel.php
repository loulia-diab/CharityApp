<?php

namespace App\Console;

use App\Jobs\ProcessMonthlySponsorships;
use App\Jobs\ProcessRecurringDonations;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new ProcessRecurringDonations)
            ->everyMinute()
            ->withoutOverlapping()
            ->onOneServer();
    }


    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
