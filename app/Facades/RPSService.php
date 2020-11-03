<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class EventCollector
 * @package App\Facades
 */
class RPSService extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \App\Services\RPS\Service::class;
    }
}
