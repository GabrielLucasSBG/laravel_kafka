<?php

namespace App\Console;

use App\Console\Commands\ConsumeKafkaUserRegistered;
use App\Console\Commands\ConsumeUserUpdated;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        ConsumeKafkaUserRegistered::class,
        ConsumeUserUpdated::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('kafka:consume-kafka-user-registered')
            ->withoutOverlapping()->runInBackground()
            ->everyFifteenSeconds();

        $schedule->command('kafka:consume-user-updated')
            ->withoutOverlapping()->runInBackground()
            ->everyFifteenSeconds();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
