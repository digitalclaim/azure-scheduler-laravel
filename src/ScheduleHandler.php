<?php

namespace DigitalClaim\AzureScheduler;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Artisan;

/**
 * ScheduleHandler
 */
class ScheduleHandler
{
    /**
     * command
     *
     * @var \DigitalClaim\AzureScheduler\ScheduleCommand
     */
    protected $command;

    /**
     * schedule
     *
     * @var \Illuminate\Console\Scheduling\Schedule
     */
    protected $schedule;

    /**
     * container
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        Schedule $schedule,
        Container $container
    ) {
        $this->schedule = $schedule;
        $this->container = $container;
    }

    /**
     * handle
     */
    public function handle(): ?string
    {
        set_time_limit(0);

        $output = $this->runCommand('schedule:run');

        return $this->cleanOutput($output);
    }

    /**
     * runCommand
     *
     * @param  mixed  $command
     */
    protected function runCommand(string $command): ?string
    {
        if ($this->isScheduledCommand($command)) {
            $scheduledCommand = $this->getScheduledCommand($command);

            if ($scheduledCommand->withoutOverlapping && ! $scheduledCommand->mutex->create($scheduledCommand)) {
                return null;
            }

            $scheduledCommand->callBeforeCallbacks($this->container);

            Artisan::call($command);

            $scheduledCommand->callAfterCallbacks($this->container);
        } else {
            Artisan::call($command);
        }

        return Artisan::output();
    }

    /**
     * isScheduledCommand
     *
     * @param  mixed  $command
     */
    protected function isScheduledCommand(string $command): bool
    {
        return ! is_null($this->getScheduledCommand($command));
    }

    /**
     * getScheduledCommand
     *
     * @param  mixed  $command
     */
    protected function getScheduledCommand(string $command): ?Event
    {
        $events = $this->schedule->events();

        foreach ($events as $event) {
            if (! is_string($event->command)) {
                continue;
            }

            $eventCommand = $this->commandWithoutArtisan($event->command);

            if ($command === $eventCommand) {
                return $event;
            }
        }

        return null;
    }

    /**
     * commandWithoutArtisan
     *
     * @param  mixed  $command
     */
    protected function commandWithoutArtisan(string $command): string
    {
        $parts = explode('artisan', $command);

        return substr($parts[1], 2, strlen($parts[1]));
    }

    /**
     * cleanOutput
     *
     * @param  mixed  $output
     */
    protected function cleanOutput(string $output): string
    {
        return trim($output);
    }
}
