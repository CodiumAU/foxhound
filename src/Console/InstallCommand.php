<?php

namespace Foxhound\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'foxhound:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the Foxhound resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->comment('Publishing Foxhound Assets...');
        $this->callSilent('vendor:publish', ['--tag' => 'foxhound-assets', '--force' => true]);

        $this->comment('Publishing Foxhound Configuration...');
        $this->callSilent('vendor:publish', ['--tag' => 'foxhound-config']);

        $this->comment('Publishing Foxhound Migrations...');
        $this->callSilent('vendor:publish', ['--tag' => 'foxhound-migrations']);

        $this->info('Foxhound scaffolding installed successfully.');
    }
}
