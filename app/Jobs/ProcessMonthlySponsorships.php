<?php

namespace App\Jobs;

use App\Models\Plan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessMonthlySponsorships implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void

    {
        $plans = Plan::where('is_activated', true)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->get();

        foreach ($plans as $plan) {
            // تسجّل تبرع، أو ترسل إشعار، أو أي منطق
        }
    }

}
