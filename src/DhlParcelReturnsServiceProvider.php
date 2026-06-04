<?php

namespace SmartDato\DhlParcelReturns;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use SmartDato\DhlParcelReturns\Commands\DhlParcelReturnsCommand;

class DhlParcelReturnsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('dhl-parcel-returns-sdk')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_dhl_parcel_returns_sdk_table')
            ->hasCommand(DhlParcelReturnsCommand::class);
    }
}
