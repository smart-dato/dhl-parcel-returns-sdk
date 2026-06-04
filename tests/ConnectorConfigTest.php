<?php

use SmartDato\DhlParcelReturns\Connectors\DhlParcelReturnsConnector;

it('uses the sandbox base url when the sandbox flag is enabled', function () {
    config()->set('dhl-parcel-returns-sdk.base_url', null);
    config()->set('dhl-parcel-returns-sdk.sandbox', true);

    expect(app(DhlParcelReturnsConnector::class)->resolveBaseUrl())
        ->toBe(DhlParcelReturnsConnector::SANDBOX_URL);
});

it('uses the production base url by default', function () {
    config()->set('dhl-parcel-returns-sdk.base_url', null);
    config()->set('dhl-parcel-returns-sdk.sandbox', false);

    expect(app(DhlParcelReturnsConnector::class)->resolveBaseUrl())
        ->toBe(DhlParcelReturnsConnector::PRODUCTION_URL);
});

it('respects an explicit base url override regardless of the sandbox flag', function () {
    config()->set('dhl-parcel-returns-sdk.base_url', 'https://example.test/returns/v1');
    config()->set('dhl-parcel-returns-sdk.sandbox', true);

    expect(app(DhlParcelReturnsConnector::class)->resolveBaseUrl())
        ->toBe('https://example.test/returns/v1');
});
