<?php

namespace DigitalClaim\AzureScheduler;

use Illuminate\Http\Request;

/**
 * Controller
 */
class Controller
{
    /**
     * @var \DigitalClaim\AzureScheduler\ScheduleHandler
     */
    protected $scheduleHandler;

    /**
     * __construct
     *
     * @param  mixed  $jobHandler
     * @return void
     */
    public function __construct(ScheduleHandler $scheduleHandler)
    {
        $this->scheduleHandler = $scheduleHandler;
    }

    /**
     * handle
     *
     * @param  mixed  $request
     * @return void
     */
    public function handle(Request $request)
    {
        return [
            'output' => $this->scheduleHandler->handle(),
        ];
    }
}
