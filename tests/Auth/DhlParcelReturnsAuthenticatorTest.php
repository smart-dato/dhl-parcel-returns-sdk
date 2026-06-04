<?php

use Saloon\Http\PendingRequest;
use SmartDato\DhlParcelReturns\Auth\DhlParcelReturnsAuthenticator;
use SmartDato\DhlParcelReturns\Connectors\DhlParcelReturnsConnector;
use SmartDato\DhlParcelReturns\Requests\Locations\GetLocationsRequest;

function buildPendingRequest(DhlParcelReturnsAuthenticator $authenticator): PendingRequest
{
    $connector = new DhlParcelReturnsConnector(
        authenticator: $authenticator,
        baseUrl: DhlParcelReturnsConnector::SANDBOX_URL,
    );

    return $connector->createPendingRequest(new GetLocationsRequest);
}

it('sends the dhl-api-key header and Basic Auth together when all credentials are provided', function () {
    $authenticator = new DhlParcelReturnsAuthenticator(
        apiKey: 'test-api-key',
        username: 'user-valid',
        password: 'SandboxPasswort2023!',
    );

    $headers = buildPendingRequest($authenticator)->headers()->all();

    expect($headers)->toHaveKey('dhl-api-key', 'test-api-key');
    expect($headers)->toHaveKey('Authorization', 'Basic '.base64_encode('user-valid:SandboxPasswort2023!'));
});

it('sends only the dhl-api-key header when no Basic Auth credentials are provided', function () {
    $authenticator = new DhlParcelReturnsAuthenticator(apiKey: 'test-api-key');

    $headers = buildPendingRequest($authenticator)->headers()->all();

    expect($headers)->toHaveKey('dhl-api-key', 'test-api-key');
    expect($headers)->not->toHaveKey('Authorization');
});

it('sends only Basic Auth when no API key is provided', function () {
    $authenticator = new DhlParcelReturnsAuthenticator(
        username: 'user-valid',
        password: 'SandboxPasswort2023!',
    );

    $headers = buildPendingRequest($authenticator)->headers()->all();

    expect($headers)->toHaveKey('Authorization', 'Basic '.base64_encode('user-valid:SandboxPasswort2023!'));
    expect($headers)->not->toHaveKey('dhl-api-key');
});

it('throws when no credentials are provided', function () {
    $authenticator = new DhlParcelReturnsAuthenticator;

    expect(fn () => buildPendingRequest($authenticator))
        ->toThrow(InvalidArgumentException::class);
});
