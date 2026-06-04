<?php

namespace SmartDato\DhlParcelReturns;

use SmartDato\DhlParcelReturns\Auth\DhlParcelReturnsAuthenticator;
use SmartDato\DhlParcelReturns\Auth\OAuthConnector;
use SmartDato\DhlParcelReturns\Connectors\DhlParcelReturnsConnector;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DhlParcelReturnsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('dhl-parcel-returns-sdk')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(DhlParcelReturnsAuthenticator::class, function () {
            return new DhlParcelReturnsAuthenticator(
                apiKey: config('dhl-parcel-returns-sdk.api_key'),
                username: config('dhl-parcel-returns-sdk.username'),
                password: config('dhl-parcel-returns-sdk.password'),
                clientSecret: config('dhl-parcel-returns-sdk.client_secret'),
                oauthBaseUrl: OAuthConnector::resolveUrl(
                    config('dhl-parcel-returns-sdk.oauth_base_url'),
                    config('dhl-parcel-returns-sdk.sandbox', false),
                ),
            );
        });

        $this->app->singleton(DhlParcelReturnsConnector::class, function ($app) {
            return new DhlParcelReturnsConnector(
                authenticator: $app->make(DhlParcelReturnsAuthenticator::class),
                baseUrl: DhlParcelReturnsConnector::resolveUrl(
                    config('dhl-parcel-returns-sdk.base_url'),
                    config('dhl-parcel-returns-sdk.sandbox', false),
                ),
            );
        });

        $this->app->singleton(DhlParcelReturns::class, function ($app) {
            return new DhlParcelReturns(
                connector: $app->make(DhlParcelReturnsConnector::class),
            );
        });
    }
}
