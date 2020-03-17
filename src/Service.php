<?php

namespace Laramate\Nucid;

use Illuminate\Container\Container;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laramate\Nucid\Contracts\Service as ServiceInterface;

class Service implements ServiceInterface
{
    /**
     * Laravel container.
     *
     * @var Container
     */
    protected $app;

    /**
     * Config.
     *
     * @var Collection
     */
    protected $config;

    /**
     * Service attributes.
     *
     * @var array
     */
    protected $attributes;

    /**
     * Nucid Server Constructor.
     *
     * @param Container $container
     * @param string    $name
     * @param array     $attributes
     * @param array     $config
     */
    public function __construct(Container $container, string $name, array $attributes, array $config)
    {
        $this->app = $container;
        $this->attributes = array_merge($attributes, compact('name'));
        $this->config = collect($config);
    }

    /**
     * Get service name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->attributes['name'];
    }

    /**
     * Get route prefix.
     *
     * @return string|null
     */
    public function getRoutePrefix(): ?string
    {
        return $this->attributes['route_prefix'] ?? null;
    }

    /**
     * Get controller namespace.
     *
     * @return string
     */
    public function getControllerNamespace(): string
    {
        $path = implode(DIRECTORY_SEPARATOR, [
            $this->config->get('base_path'),
            $this->relative_path,
            $this->config->get('controllers_dir'),
        ]);

        return ucfirst(str_replace(DIRECTORY_SEPARATOR, '\\', $path));
    }

    /**
     * Determinate the controller path.
     *
     * @param string $path
     *
     * @return string
     */
    public function controllerPath(string $path = ''): string
    {
        $basePath = $this->path($this->config->get('controllers_dir'));

        return $basePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Determinate the service provider path.
     *
     * @param string $path
     *
     * @return string
     */
    public function providerPath(string $path = ''): string
    {
        $basePath = $this->path($this->config->get('providers_dir'));

        return $basePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Determinate the route path.
     *
     * @param string $path
     *
     * @return string
     */
    public function routePath(string $path = ''): string
    {
        $basePath = $this->path($this->config->get('routes_dir'));

        return $basePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Determinate the feature path.
     *
     * @param string $path
     *
     * @return string
     */
    public function featurePath(string $path = ''): string
    {
        $basePath = $this->path($this->config->get('features_dir'));

        return $basePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Determinate the resource path.
     *
     * @param string $path
     *
     * @return string
     */
    public function resourcePath(string $path = ''): string
    {
        $basePath = $this->path($this->config->get('resources_dir'));

        return $basePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Determinate the request path.
     *
     * @param string $path
     *
     * @return string
     */
    public function requestPath(string $path = ''): string
    {
        $basePath = $this->path($this->config->get('requests_dir'));

        return $basePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Determinate the current service path.
     *
     * @param string $path
     *
     * @return string
     */
    public function path(string $path = '')
    {
        $basePath = $this->basePath($this->relative_path);

        return $basePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Determinate the services base path.
     *
     * @param string $path
     *
     * @return string
     */
    public function basePath(string $path = '')
    {
        $basePath = base_path($this->config->get('base_path'));

        return $basePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the service based route name.
     *
     * @param string|null $name
     *
     * @return string
     */
    public function getRouteName(?string $name = null): string
    {
        return collect([
                preg_replace('/\.$/', '', $this->getSubDomain()),
                $this->getRoutePrefix(),
                $name,
            ])
            ->filter()
            ->implode('.');
    }

    /**
     * Get domain.
     *
     * @return string
     */
    public function getDomain(): ?string
    {
        return ! empty($_SERVER['HTTP_HOST'])
            ? strtolower($_SERVER['HTTP_HOST'])
            : null;
    }

    /**
     * Get sub domain.
     *
     * @return string|null
     */
    public function getSubDomain(): ?string
    {
        return $this->subdomain ?? null;
    }

    /**
     * Register service routes.
     */
    public function registerRoutes()
    {
        if (! is_readable($this->routesFile())) {
            return;
        }

        $route = Route::name($this->getName().'.');

        if ($this->getRoutePrefix()) {
            $route->prefix($this->getRoutePrefix());
        }

        if ($this->getSubDomain()) {
            $route->domain($this->getDomain() ?? $this->getSubDomain().'*');
        }

        if ($this->middleware) {
            $route->middleware($this->middleware);
        }

        $route->namespace($this->getControllerNamespace());
        $route->group($this->routesFile());
    }

    /**
     * Get service providers.
     *
     * @return array
     */
    public function getServiceProviders(): array
    {
        if (File::exists($this->serviceConfigFile())) {
            $config = include $this->serviceConfigFile();
        }

        return $config['providers'] ?? [];
    }

    /**
     * Get service aliases.
     *
     * @return array
     */
    public function getServiceAliases(): array
    {
        if (File::exists($this->serviceConfigFile())) {
            $config = include $this->serviceConfigFile();
        }

        return $config['aliases'] ?? [];
    }

    /**
     * Ensure service directories are existing.
     */
    public function ensureDirectoriesExisting()
    {
        $paths = [
            $this->controllerPath(),
            $this->providerPath(),
            $this->routePath(),
            $this->featurePath(),
            $this->resourcePath(),
            $this->requestPath(),
        ];

        foreach ($paths as $path) {
            File::ensureDirectoryExists($path, 0775, true);
        }
    }

    /**
     * Get the service routes file with full path.
     *
     * @return string
     */
    public function routesFile(): string
    {
        $file = $this->config->get('routes_file');

        return $this->routePath($file);
    }

    /**
     * Get the service config file with full path.
     *
     * @return string
     */
    protected function serviceConfigFile(): string
    {
        $file = $this->config->get('service_config_file');

        return $this->path($file);
    }

    /**
     * Ensure service files are existing.
     */
    public function ensureFilesExisting()
    {
        $this->ensureFileExists($this->routesFile(), $this->routesFileContent());
        $this->ensureFileExists($this->serviceConfigFile(), $this->serviceConfigFileContent());
    }

    /**
     * Create file with content, if it does't exists.
     *
     * @param string $file
     * @param string $content
     */
    protected function ensureFileExists(string $file, string $content = '')
    {
        if (! File::exists($file)) {
            File::put($file, $content);
        }
    }

    /**
     * Get the routes file content.
     *
     * @return string
     */
    protected function routesFileContent(): string
    {
        return file_get_contents(
            implode(DIRECTORY_SEPARATOR, [__DIR__, 'Files', 'Routes.php'])
        );
    }

    /**
     * Get the service config file content.
     *
     * @return string
     */
    protected function serviceConfigFileContent(): string
    {
        return file_get_contents(
            implode(DIRECTORY_SEPARATOR, [__DIR__, 'Files', 'ServiceConfig.php'])
        );
    }

    /**
     * Magic getter.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $mutator = sprintf('get%sAttribute', Str::studly($name));

        if (method_exists($this, $mutator)) {
            return $this->$mutator();
        }

        return $this->attributes[$name] ?? null;
    }
}
