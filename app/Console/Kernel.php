<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        Commands\GeneralStatistics::class,
        Commands\GeneralUserStatistics::class,
        Commands\OrderFinished::class,
        Commands\UserReferrerLevel::class,
        Commands\GeneralDirector::class,
        Commands\OrderEquityCommand::class,
        Commands\GoldDay::class,
        Commands\GoldDayUser::class,
        Commands\VipHandel::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('OrderFinished')->daily();
        $schedule->command('GeneralStatistics')->dailyAt('1:00');
        $schedule->command('GoldDayUser')->dailyAt('0:05');
        $schedule->command('GoldDay')->dailyAt('21:00');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
