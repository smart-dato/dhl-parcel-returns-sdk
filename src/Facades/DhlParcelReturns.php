<?php

namespace SmartDato\DhlParcelReturns\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \SmartDato\DhlParcelReturns\DhlParcelReturns
 */
class DhlParcelReturns extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \SmartDato\DhlParcelReturns\DhlParcelReturns::class;
    }
}
