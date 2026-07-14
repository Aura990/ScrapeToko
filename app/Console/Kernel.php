<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Cek otomatis setiap 6 jam apakah toko saingan sudah lebih murah dari toko dikelola,
        // berdasarkan kombinasi toko & kata kunci yang pernah dibandingkan pengguna.
        $schedule->command('app:check-price-drops')->everySixHours()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
