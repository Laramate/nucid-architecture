<?php

namespace Laramate\Nucid;

use Illuminate\Container\Container;
use Laramate\Nucid\Contracts\Service as ServiceInterface;

class Helper
{
    /**
     * Laravel container.
     *
     * @var Container
     */
    protected $app;

    /**
     * Nucid service manager.
     *
     * @var ServiceManager
     */
    protected $manager;

    /**
     * Helper constructor.
     *
     * @param Container      $app
     * @param ServiceManager $manager
     */
    public function __construct(Container $app, ServiceManager $manager)
    {
        $this->app = $app;
        $this->manager = $manager;
    }

    /**
     * @param string $name
     * @param array  $parameters
     * @param bool   $absolute
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return string
     */
    public function route($name = '', array $parameters = [], bool $absolute = true): string
    {
        $name = $this->manager->getCurrent()->getRouteName($name);

        return $this->app
            ->make('url')
            ->route($name, $parameters, $absolute) ?? '';
    }

    /**
     * Get the current service.
     *
     * @return ServiceInterface
     */
    public function currentService(): ServiceInterface
    {
        return $this->manager->getCurrent();
    }

    /**
     * Get the name of the current service.
     *
     * @return string
     */
    public function currentServiceName(): string
    {
        return $this->manager->getCurrent()->getName();
    }
}
