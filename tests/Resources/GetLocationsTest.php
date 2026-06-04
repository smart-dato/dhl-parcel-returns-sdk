<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use SmartDato\DhlParcelReturns\Auth\DhlParcelReturnsAuthenticator;
use SmartDato\DhlParcelReturns\Connectors\DhlParcelReturnsConnector;
use SmartDato\DhlParcelReturns\Data\Locations\ReceiverData;
use SmartDato\DhlParcelReturns\DhlParcelReturns;
use SmartDato\DhlParcelReturns\Enums\Country;
use SmartDato\DhlParcelReturns\Requests\Locations\GetLocationsRequest;

function locationsClient(MockClient $mockClient): DhlParcelReturns
{
    $connector = new DhlParcelReturnsConnector(
        authenticator: new DhlParcelReturnsAuthenticator(apiKey: 'k', username: 'u', password: 'p'),
        baseUrl: DhlParcelReturnsConnector::SANDBOX_URL,
    );
    $connector->withMockClient($mockClient);

    return new DhlParcelReturns($connector);
}

function receiverPayload(): array
{
    return [
        'receiverId' => 'deu',
        'shipperCountry' => 'deu',
        'ekp' => '2222222222',
        'billingNumber' => '22222222220701',
        'companyName' => 'DHL Paket GmbH',
        'numberRange' => '99999XXXXXXC',
        'receiverAddress' => [
            'name1' => 'Retoure Center',
            'addressStreet' => 'Sträßchensweg',
            'addressHouse' => '10',
            'postalCode' => '53113',
            'city' => 'Bonn',
        ],
        'companyAddress' => [
            'name1' => 'DHL Paket GmbH',
            'addressStreet' => 'Charles-de-Gaulle-Str.',
            'addressHouse' => '20',
            'postalCode' => '53113',
            'city' => 'Bonn',
        ],
        'contactEmail' => 'returns@dhl.local',
    ];
}

it('returns a list of hydrated receivers', function () {
    $mockClient = new MockClient([
        GetLocationsRequest::class => MockResponse::make([receiverPayload()]),
    ]);

    $receivers = locationsClient($mockClient)->locations()->get(country: Country::Deu);

    expect($receivers)->toBeArray()->toHaveCount(1);
    expect($receivers[0])->toBeInstanceOf(ReceiverData::class);
    expect($receivers[0]->receiverId)->toBe('deu');
    expect($receivers[0]->shipperCountry)->toBe(Country::Deu);
    expect($receivers[0]->billingNumber)->toBe('22222222220701');
    expect($receivers[0]->receiverAddress->city)->toBe('Bonn');
});

it('passes the supported filters as query parameters', function () {
    $mockClient = new MockClient([
        GetLocationsRequest::class => MockResponse::make([]),
    ]);

    locationsClient($mockClient)->locations()->get(
        country: Country::Deu,
        postalCode: '53113',
        receiverId: 'deu',
        billingNumber: '22222222220701',
        maxResult: 4,
    );

    expect($mockClient->getLastPendingRequest()->query()->all())->toBe([
        'countryCode' => 'deu',
        'postalCode' => '53113',
        'receiverId' => 'deu',
        'billingNumber' => '22222222220701',
        'maxResult' => 4,
    ]);
});

it('sends no filters when none are provided', function () {
    $mockClient = new MockClient([
        GetLocationsRequest::class => MockResponse::make([]),
    ]);

    locationsClient($mockClient)->locations()->get();

    expect($mockClient->getLastPendingRequest()->query()->all())->toBe([]);
});
