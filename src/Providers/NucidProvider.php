<?php

namespace Laramate\Nucid\Providers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;
use Laramate\Nucid\Commands\NucidCreateDirectories;
use Laramate\Nucid\Commands\NucidCreateFiles;
use Laramate\Nucid\Commands\NucidInstall;
use Laramate\Nucid\Exceptions\NucidException;
use Laramate\Nucid\ServiceManager;

class NucidProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @throws BindingResolutionException
     */
    public function register()
    {
        // Add Nucid configuration
        $this->mergeConfigFrom($this->path('Config/NucidFullConfig.php'), 'nucid');

        // Load the Nucid Service Manager and do additional registrations
        $manager = $this->app->make(ServiceManager::class);
        $manager->register();

        // Bind the Nucid Service Manager into Laravel's service container
        $this->app->instance(ServiceManager::class, $manager);
    }

    /**
     * Bootstrap any application services.
     *
     * @throws NucidException
     */
    public function boot(ServiceManager $manager)
    {
        $manager->boot();
        $this->addCommands();
        $this->bootPublishing();
        $this->bootViews();
    }

    /**
     * Register aliases.
     */
    protected function addCommands()
    {
        $this->commands([
            NucidInstall::class,
            NucidCreateDirectories::class,
            NucidCreateFiles::class,
        ]);
    }

    /**
     * Boot publishing.
     */
    protected function bootPublishing()
    {
        $this->publishes(
            [$this->path('Config/NucidServices.php') => config_path('nucid.php')],
            'nucid-config-only-services'
        );

        $this->publishes(
            [$this->path('Config/NucidFullConfig.php') => config_path('nucid.php')],
            'nucid-config-full'
        );
    }

    /**
     * Boot views.
     *
     * @throws NucidException
     */
    protected function bootViews()
    {
        $this->loadViewsFrom($this->path('Views'), 'Nucid');
    }

    /**
     * Get package path.
     *
     * @param string $to
     *
     * @throws NucidException
     *
     * @return string
     */
    protected function path(string $to = ''): string
    {
        if ($path = realpath(__DIR__.'/../'.$to)) {
            return $path;
        }

        throw new NucidException('Path not valid.');
    }
}
