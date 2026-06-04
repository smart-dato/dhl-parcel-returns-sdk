<?php

namespace SmartDato\DhlParcelReturns\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \SmartDato\DhlParcelReturns\DhlParcelReturns
 *
 * @method static \SmartDato\DhlParcelReturns\Resources\OrdersResource orders()
 * @method static \SmartDato\DhlParcelReturns\Resources\LocationsResource locations()
 * @method static \SmartDato\DhlParcelReturns\Resources\GeneralResource general()
 */
class DhlParcelReturns extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \SmartDato\DhlParcelReturns\DhlParcelReturns::class;
    }
}
