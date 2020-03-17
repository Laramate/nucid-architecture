<?php

namespace Laramate\Nucid\Commands;

use Illuminate\Console\Command;
use Laramate\Nucid\ServiceManager;

class NucidCreateDirectories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nucid:create-directories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Nucid directories';

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
        $this->manager->ensureDirectoriesExisting();

        $this->info('Directories were created as defined in the Nucid configuration');
    }
}
