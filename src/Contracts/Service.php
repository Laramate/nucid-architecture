<?php

namespace Laramate\Nucid\Contracts;

interface Service
{
    /**
     * Get service name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get route prefix.
     *
     * @return string|null
     */
    public function getRoutePrefix(): ?string;

    /**
     * Get domain.
     *
     * @return string
     */
    public function getDomain(): ?string;

    /**
     * Get sub domain.
     *
     * @return string|null
     */
    public function getSubDomain(): ?string;

    /**
     * Register service routes.
     */
    public function registerRoutes();

    /**
     * Get service providers.
     *
     * @return array
     */
    public function getServiceProviders(): array;

    /**
     * Get service aliases.
     *
     * @return array
     */
    public function getServiceAliases(): array;

    /**
     * Ensure service directories are existing.
     */
    public function ensureDirectoriesExisting();

    /**
     * Ensure service files are existing.
     */
    public function ensureFilesExisting();

    /**
     * Get the service routes file with full path.
     *
     * @return string
     */
    public function routesFile(): string;
}
