<?php

namespace Laramate\Nucid\Facades;

use Laramate\Composite\Support\FacadeMapper\Facades\Facade;

class Helper extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Laramate\Nucid\Helper::class;
    }
}
