<?php

declare(strict_types=1);

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('news:fetch')
            ->everySixHours()
            ->withoutOverlapping()
            ->runInBackground()
            ->onSuccess(function () {
                logger()->info('News articles fetched successfully');
            })
            ->onFailure(function () {
                logger()->error('News articles fetch failed');
            });
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
