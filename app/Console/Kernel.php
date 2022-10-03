<?php

namespace App\Console;

use App\Models\User;
use App\Jobs\digest;
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
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        $schedule->command('queue:work --stop-when-empty')->everyMinute();

        $schedule->call(function () {
            $count = User::with('getNotApproved')->count();
            $admins = User::where('role_id', '=', 3)->get();
            foreach ($admins as $admin) {
                $e = $admin->email;
                dispatch(new digest($e, $count));
            }
        })->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
