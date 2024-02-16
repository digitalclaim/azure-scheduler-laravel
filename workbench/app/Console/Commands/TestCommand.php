<?php

namespace Workbench\App\Console\Commands;

use Illuminate\Console\Command;

/**
 * TestCommand
 */
class TestCommand extends Command
{
    /**
     * signature
     *
     * @var string
     */
    protected $signature = 'test:command';

    /**
     * description
     *
     * @var string
     */
    protected $description = 'Do some testy stuff';

    /**
     * handle
     *
     * @return void
     */
    public function handle()
    {
        sleep(10);
    }
}
