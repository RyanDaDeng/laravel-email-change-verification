<?php

namespace TimeHunter\LaravelEmailChangeVerification\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelEmailChangeVerification extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravelemailchangeverification';
    }
}
