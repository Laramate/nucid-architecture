<?php

namespace Laramate\Nucid\Commands;

use Illuminate\Console\Command;
use Laramate\Nucid\ServiceManager;

class NucidCreateFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nucid:create-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Nucid files';

    /**
     * Nucid service manager.
     *
     * @var ServiceManager
     */
    protected $manager;

    /**
     * Create a new command instance.
     */
    public function __construct(ServiceManager $manager)
    {
        parent::__construct();

        $this->manager = $manager;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->manager->ensureFilesExisting();

        $this->info('Files were created as defined in the Nucid configuration');
    }
}
