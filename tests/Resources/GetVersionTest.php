<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use SmartDato\DhlParcelReturns\Auth\DhlParcelReturnsAuthenticator;
use SmartDato\DhlParcelReturns\Connectors\DhlParcelReturnsConnector;
use SmartDato\DhlParcelReturns\DhlParcelReturns;
use SmartDato\DhlParcelReturns\Requests\General\GetVersionRequest;

function versionConnector(): DhlParcelReturnsConnector
{
    return new DhlParcelReturnsConnector(
        authenticator: new DhlParcelReturnsAuthenticator(apiKey: 'k', username: 'u', password: 'p'),
        baseUrl: DhlParcelReturnsConnector::SANDBOX_URL,
    );
}

it('hydrates the version from the amp envelope', function () {
    $connector = versionConnector();
    $connector->withMockClient(new MockClient([
        GetVersionRequest::class => MockResponse::make([
            'amp' => [
                'name' => 'pp-parcel-returns',
                'version' => 'v1.0.0',
                'rev' => '13',
                'env' => 'dev',
            ],
        ]),
    ]));

    $version = (new DhlParcelReturns($connector))->general()->version();

    expect($version->name)->toBe('pp-parcel-returns')
        ->and($version->version)->toBe('v1.0.0')
        ->and($version->rev)->toBe('13')
        ->and($version->env)->toBe('dev');
});

it('does not send authentication headers for the public version endpoint', function () {
    $headers = versionConnector()
        ->createPendingRequest(new GetVersionRequest)
        ->headers()
        ->all();

    expect($headers)->not->toHaveKey('dhl-api-key');
    expect($headers)->not->toHaveKey('Authorization');
});
