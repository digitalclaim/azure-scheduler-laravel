<?php

namespace Workbench\App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Workbench\App\Console\Commands\TestCommand;

/**
 * Kernel
 */
class Kernel extends ConsoleKernel
{
    protected $commands = [
        TestCommand::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // $schedule->call(function () {
        //     Log::info('Test this fucking scheduler!');
        // })->name('some-command')->everyFiveSeconds();

        $schedule->command('test:command')->everyFiveSeconds()->withoutOverlapping();

        // $schedule->command('test:command2')->before(function () {
        //     Log::info('log after');
        // })->after(function () {
        //     Log::warning('log before');
        // });
        // $schedule->call(function () {
        //     Log::info('log call');
        // });
    }
}
