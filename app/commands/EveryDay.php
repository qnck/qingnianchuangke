<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class EveryDay extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'command:EveryDay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run daily cron job. DO NOT run this manually.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        try {
            LogCronjob::addLog($this->name, 'START-CRONJOB');
        } catch (Exception $e) {
            if ($e->getCode() > 2000) {
                LogCronjob::addLog($this->name, $e->getMessage());
            }
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            // ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            // ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
