<?php

namespace Laramate\Nucid;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Laramate\Nucid\Contracts\Service as ServiceInterface;
use Laramate\Nucid\Exceptions\NucidException;
use Laramate\Nucid\Facades\Helper;

class ServiceManager
{
    /**
     * Application container.
     *
     * @var Container
     */
    protected $app;

    /**
     * Nucid config.
     *
     * @var Collection
     */
    protected $config;

    /**
     * Services.
     *
     * @var array
     */
    protected $services;

    /**
     * Default service.
     *
     * @var Service
     */
    protected $default;

    /**
     * Current service.
     *
     * @var Service
     */
    protected $current;

    /**
     * NucidServicesManager constructor.
     *
     * @param Repository $config
     * @param Container  $container
     */
    public function __construct(Repository $config, Container $container)
    {
        $this->config = collect($config->get('nucid'));

        $this->app = $container;

        $this->configure();
    }

    /**
     * Configure services.
     *
     * @throws NucidException
     */
    protected function configure()
    {
        $this->services = $this->makeServices();
        $this->current = &$this->services[$this->determinateCurrentService()];
    }

    protected function makeServices()
    {
        return collect($this->config->get('services') ?? [])
            ->map(function (array $attributes, string $name) {
                return $this->app->make(Service::class, [
                    'name'       => $name,
                    'attributes' => $attributes,
                    'config'     => $this->config->all(),
                ]);
            })
            ->sortBy(function (ServiceInterface $service) {
                return $service->getRoutePrefix() ? 0 : 1;
            })
            ->sortBy(function (ServiceInterface $service) {
                return $service->getSubDomain() ? 0 : 1;
            })
            ->toArray();
    }

    /**
     * Determinate current (active) service.
     */
    protected function determinateCurrentService(): string
    {
        $requestUri = strtolower($_SERVER['REQUEST_URI'] ?? '');

        /** @var ServiceInterface $service */
        foreach ($this->services as $name=>$service) {
            if ($service->getSubDomain() && ! $this->matchesSubdomain($service->subdomain)) {
                continue;
            }

            if ($service->getRoutePrefix() && ! preg_match('/[\/]+'.$service->getRoutePrefix().'*/i', $requestUri)) {
                continue;
            }

            return $name;
        }

        throw new NucidException('Unable to determinate the Nucid Service.');
    }

    public function register()
    {
        // Register current service providers
        foreach ($this->current->getServiceProviders() as $provider) {
            $this->app->register($provider);
        }

        // Register current service aliases
        foreach ($this->current->getServiceAliases() as $alias=>$class) {
            AliasLoader::getInstance()->alias($alias, $class);
        }

        // Register Nucid Helper alias
        AliasLoader::getInstance()->alias('Nucid', Helper::class);
    }

    /**
     * Initialize.
     */
    public function boot()
    {
        if (! $this->config->get('state')) {
            return;
        }
        $this->registerRoutes();
    }

    /**
     * Get http host.
     *
     * @return string
     */
    protected function getHttpHost(): string
    {
        return strtolower($_SERVER['HTTP_HOST'] ?? '');
    }

    /**
     * Get base host.
     *
     * @return string
     */
    protected function getBaseHost(): string
    {
        return str_replace('www.', '', $this->getHttpHost());
    }

    /**
     * Determinate if the given value matches the sub domain.
     *
     * @param string $subdomain
     *
     * @return bool
     */
    protected function matchesSubdomain(string $subdomain): bool
    {
        return 0 === strpos($this->getBaseHost(), $subdomain);
    }

    /**
     * Register routes.
     */
    protected function registerRoutes()
    {
        if ($this->app->runningInConsole()) {
            return $this->registerAllRoutes();
        }

        $this->current->registerRoutes();
    }

    /**
     * Register the routes of all servives.
     */
    protected function registerAllRoutes()
    {
        foreach ($this->services as $service) {
            $service->registerRoutes();
        }
    }

    /**
     * Get the current (active) service.
     *
     * @return Service
     */
    public function getCurrent(): Service
    {
        return $this->current;
    }

    /**
     * Get all available services.
     *
     * @return array
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * Determinate the services base path.
     *
     * @param string $path
     *
     * @return string
     */
    protected function basePath(string $path = ''): string
    {
        $basePath = base_path($this->config->get('base_path'));

        return $basePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Determinate the domains path.
     *
     * @param string $path
     *
     * @return string
     */
    protected function domainsPath(string $path = ''): string
    {
        $basePath = base_path($this->config->get('domains_path'));

        return $basePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Ensure service directories are existing.
     */
    public function ensureDirectoriesExisting()
    {
        File::ensureDirectoryExists($this->basePath());
        File::ensureDirectoryExists($this->domainsPath());

        /** @var ServiceInterface $service */
        foreach ($this->services as $service) {
            $service->ensureDirectoriesExisting();
        }
    }

    /**
     * Ensure service files are existing.
     */
    public function ensureFilesExisting()
    {
        /** @var ServiceInterface $service */
        foreach ($this->services as $service) {
            $service->ensureFilesExisting();
        }
    }
}
