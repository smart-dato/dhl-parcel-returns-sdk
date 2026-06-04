<?php

namespace SmartDato\DhlParcelReturns;

use SmartDato\DhlParcelReturns\Auth\DhlParcelReturnsAuthenticator;
use SmartDato\DhlParcelReturns\Auth\OAuthConnector;
use SmartDato\DhlParcelReturns\Connectors\DhlParcelReturnsConnector;
use SmartDato\DhlParcelReturns\Resources\GeneralResource;
use SmartDato\DhlParcelReturns\Resources\LocationsResource;
use SmartDato\DhlParcelReturns\Resources\OrdersResource;

class DhlParcelReturns
{
    private ?OrdersResource $ordersResource = null;

    private ?LocationsResource $locationsResource = null;

    private ?GeneralResource $generalResource = null;

    public function __construct(
        protected DhlParcelReturnsConnector $connector,
    ) {}

    public function orders(): OrdersResource
    {
        return $this->ordersResource ??= new OrdersResource($this->connector);
    }

    public function locations(): LocationsResource
    {
        return $this->locationsResource ??= new LocationsResource($this->connector);
    }

    public function general(): GeneralResource
    {
        return $this->generalResource ??= new GeneralResource($this->connector);
    }

    /**
     * @param array{
     *     api_key?: string|null,
     *     username?: string|null,
     *     password?: string|null,
     *     client_secret?: string|null,
     *     base_url?: string,
     *     oauth_base_url?: string|null,
     *     sandbox?: bool,
     * } $config
     */
    public static function make(array $config = []): self
    {
        $sandbox = $config['sandbox'] ?? false;

        $authenticator = new DhlParcelReturnsAuthenticator(
            apiKey: $config['api_key'] ?? null,
            username: $config['username'] ?? null,
            password: $config['password'] ?? null,
            clientSecret: $config['client_secret'] ?? null,
            oauthBaseUrl: OAuthConnector::resolveUrl($config['oauth_base_url'] ?? null, $sandbox),
        );

        $baseUrl = DhlParcelReturnsConnector::resolveUrl(
            $config['base_url'] ?? null,
            $sandbox,
        );

        $connector = new DhlParcelReturnsConnector(
            authenticator: $authenticator,
            baseUrl: $baseUrl,
        );

        return new self($connector);
    }
}
