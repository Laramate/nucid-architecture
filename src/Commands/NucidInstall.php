<?php

namespace Laramate\Nucid\Commands;

use Illuminate\Console\Command;
use Laramate\Nucid\ServiceManager;

class NucidInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nucid:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Nucid';

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
        $this->manager->ensureFilesExisting();

        $this->info('Nucid was installed successfully');
    }
}
