<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('sms:send-appointment-sms')->hourly();
        $schedule->command('sms:send-review-request-sms')->dailyAt('15:00');
        $schedule->command('sms:send-new-year-greetings-sms')->yearlyOn(12, 31, '13:00');
        $schedule->command('db:backup')->daily();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
