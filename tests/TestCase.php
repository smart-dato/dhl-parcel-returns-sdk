<?php

namespace SmartDato\DhlParcelReturns\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use SmartDato\DhlParcelReturns\DhlParcelReturnsServiceProvider;
use Spatie\LaravelData\LaravelDataServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelDataServiceProvider::class,
            DhlParcelReturnsServiceProvider::class,
        ];
    }
}
