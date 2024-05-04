<?php

namespace App\Console;

use App\Http\Controllers\API\ScheduleController;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        // $schedule->call(function () {
        //     // Log::info('Method getShippingMD is executed at: ' . now());
        //     $controller = new ScheduleController();
        //     return $controller->syncShippingOrderMD();
        // })->everyMinute();

        $schedule->call(function () {
            Log::info('Method getShippingMD is executed at: ' . now());

            $controller = new ScheduleController();
            return  $controller->syncShippingOrderMD();
        })->everyTenMinutes();
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
