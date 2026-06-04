<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use SmartDato\DhlParcelReturns\Auth\DhlParcelReturnsAuthenticator;
use SmartDato\DhlParcelReturns\Connectors\DhlParcelReturnsConnector;
use SmartDato\DhlParcelReturns\Data\Orders\ContactAddressData;
use SmartDato\DhlParcelReturns\Data\Orders\ReturnOrderData;
use SmartDato\DhlParcelReturns\Data\Orders\ValueData;
use SmartDato\DhlParcelReturns\Data\Orders\WeightData;
use SmartDato\DhlParcelReturns\DhlParcelReturns;
use SmartDato\DhlParcelReturns\Enums\Currency;
use SmartDato\DhlParcelReturns\Enums\LabelType;
use SmartDato\DhlParcelReturns\Enums\WeightUom;
use SmartDato\DhlParcelReturns\Requests\Orders\CreateReturnOrderRequest;

function returnsClient(MockClient $mockClient): DhlParcelReturns
{
    $connector = new DhlParcelReturnsConnector(
        authenticator: new DhlParcelReturnsAuthenticator(apiKey: 'k', username: 'u', password: 'p'),
        baseUrl: DhlParcelReturnsConnector::SANDBOX_URL,
    );
    $connector->withMockClient($mockClient);

    return new DhlParcelReturns($connector);
}

function germanReturnOrder(): ReturnOrderData
{
    return new ReturnOrderData(
        receiverId: 'deu',
        shipper: new ContactAddressData(
            name1: 'Absender Retoure Zeile 1',
            addressStreet: 'Charles-de-Gaulle Str.',
            addressHouse: '20',
            postalCode: '53113',
            city: 'Bonn',
            email: 'beispiel@beispiel.de',
            phone: '+49 421 987654321',
            state: 'NRW',
        ),
        customerReference: 'Kundenreferenz',
        shipmentReference: 'Sendungsreferenz',
        itemWeight: new WeightData(WeightUom::Grams, 1000),
        itemValue: new ValueData(value: 100, currency: Currency::Eur),
    );
}

it('creates a return label and hydrates the confirmation', function () {
    $mockClient = new MockClient([
        CreateReturnOrderRequest::class => MockResponse::make(
            body: [
                'sstatus' => ['title' => 'Created', 'status' => 201, 'detail' => 'Created'],
                'shipmentNo' => '999991587211',
                'label' => ['b64' => 'iVBORw0KGgoAAAANSUhEUgAA'],
                'qrLabel' => ['b64' => 'cVBORw0KGgoAAAANSUhEUgAA'],
                'qrLink' => 'https://app.dhl.de/import?token=abc',
                'routingCode' => '40327653113+99000933090010',
            ],
            status: 201,
        ),
    ]);

    $confirmation = returnsClient($mockClient)->orders()->create(germanReturnOrder(), LabelType::Both);

    expect($confirmation->shipmentNo)->toBe('999991587211')
        ->and($confirmation->routingCode)->toBe('40327653113+99000933090010')
        ->and($confirmation->label->b64)->toBe('iVBORw0KGgoAAAANSUhEUgAA')
        ->and($confirmation->qrLabel->b64)->toBe('cVBORw0KGgoAAAANSUhEUgAA')
        ->and($confirmation->qrLink)->toBe('https://app.dhl.de/import?token=abc')
        ->and($confirmation->status->title)->toBe('Created')
        ->and($confirmation->status->status)->toBe(201);
});

it('serializes the order body and the labelType query as DHL expects', function () {
    $mockClient = new MockClient([
        CreateReturnOrderRequest::class => MockResponse::make(
            body: [
                'sstatus' => ['title' => 'Created', 'status' => 201],
                'shipmentNo' => '999991587211',
                'label' => ['b64' => 'AAAA'],
                'routingCode' => 'rc',
            ],
            status: 201,
        ),
    ]);

    returnsClient($mockClient)->orders()->create(germanReturnOrder(), LabelType::ShipmentLabel);

    $pendingRequest = $mockClient->getLastPendingRequest();
    $body = $pendingRequest->body()->all();

    expect($pendingRequest->query()->all())->toBe(['labelType' => 'SHIPMENT_LABEL']);
    expect($body['receiverId'])->toBe('deu');
    expect($body['shipper']['addressHouse'])->toBe('20');
    expect($body['shipper']['postalCode'])->toBe('53113');
    expect($body['itemWeight'])->toBe(['uom' => 'g', 'value' => 1000.0]);
    expect($body['itemValue'])->toBe(['value' => 100.0, 'currency' => 'EUR']);
});

it('prunes unset optional fields from the order body', function () {
    $mockClient = new MockClient([
        CreateReturnOrderRequest::class => MockResponse::make(
            body: [
                'sstatus' => ['title' => 'Created', 'status' => 201],
                'shipmentNo' => '1',
                'label' => ['b64' => 'AAAA'],
                'routingCode' => 'rc',
            ],
            status: 201,
        ),
    ]);

    $order = new ReturnOrderData(
        receiverId: 'deu',
        shipper: new ContactAddressData(
            name1: 'Max Mustermann',
            addressStreet: 'Charles-de-Gaulle Str.',
            addressHouse: '20',
            postalCode: '53113',
            city: 'Bonn',
        ),
    );

    returnsClient($mockClient)->orders()->create($order);

    $body = $mockClient->getLastPendingRequest()->body()->all();

    expect($body)->toBe([
        'receiverId' => 'deu',
        'shipper' => [
            'name1' => 'Max Mustermann',
            'addressStreet' => 'Charles-de-Gaulle Str.',
            'addressHouse' => '20',
            'postalCode' => '53113',
            'city' => 'Bonn',
        ],
    ]);
});

it('omits the labelType query when none is given', function () {
    $mockClient = new MockClient([
        CreateReturnOrderRequest::class => MockResponse::make(
            body: [
                'sstatus' => ['title' => 'Created', 'status' => 201],
                'shipmentNo' => '1',
                'label' => ['b64' => 'AAAA'],
                'routingCode' => 'rc',
            ],
            status: 201,
        ),
    ]);

    returnsClient($mockClient)->orders()->create(germanReturnOrder());

    expect($mockClient->getLastPendingRequest()->query()->all())->toBe([]);
});
