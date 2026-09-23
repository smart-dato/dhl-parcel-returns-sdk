# DHL Parcel DE Returns SDK for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/smart-dato/dhl-parcel-returns-sdk.svg?style=flat-square)](https://packagist.org/packages/smart-dato/dhl-parcel-returns-sdk)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/smart-dato/dhl-parcel-returns-sdk/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/smart-dato/dhl-parcel-returns-sdk/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/smart-dato/dhl-parcel-returns-sdk/code-style.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/smart-dato/dhl-parcel-returns-sdk/actions?query=workflow%3A%22Code+style%22+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/smart-dato/dhl-parcel-returns-sdk.svg?style=flat-square)](https://packagist.org/packages/smart-dato/dhl-parcel-returns-sdk)

A Laravel package for the [DHL Parcel DE Returns API](https://developer.dhl.com/api-reference/parcel-de-returns-post-parcel-germany). Create return labels on demand and look up return receiver locations. Built on [Saloon](https://docs.saloon.dev) and [Spatie Laravel Data](https://spatie.be/docs/laravel-data).

## Installation

```bash
composer require smart-dato/dhl-parcel-returns-sdk
```

Publish the config file:

```bash
php artisan vendor:publish --tag="dhl-parcel-returns-sdk-config"
```

Add your credentials to `.env`:

```env
# OAuth2 (recommended — Basic Auth is deprecated by DHL):
DHL_PARCEL_RETURNS_API_KEY=your-client-id
DHL_PARCEL_RETURNS_CLIENT_SECRET=your-client-secret
DHL_PARCEL_RETURNS_USERNAME=your-business-customer-username
DHL_PARCEL_RETURNS_PASSWORD=your-business-customer-password

# Or legacy API key only:
# DHL_PARCEL_RETURNS_API_KEY=your-api-key

# Or legacy Basic Auth:
# DHL_PARCEL_RETURNS_USERNAME=your-username
# DHL_PARCEL_RETURNS_PASSWORD=your-password

# Enable sandbox for testing:
# DHL_PARCEL_RETURNS_SANDBOX=true
```

OAuth2 mode is enabled automatically when `DHL_PARCEL_RETURNS_CLIENT_SECRET` is set
together with the API key, username and password. The SDK then exchanges the
credentials for a Bearer token against the DHL ROPC token endpoint and caches
the token in memory until it expires.

## Usage

### Create a return label

```php
use SmartDato\DhlParcelReturns\Data\Orders\ContactAddressData;
use SmartDato\DhlParcelReturns\Data\Orders\ReturnOrderData;
use SmartDato\DhlParcelReturns\Data\Orders\ValueData;
use SmartDato\DhlParcelReturns\Data\Orders\WeightData;
use SmartDato\DhlParcelReturns\Enums\Currency;
use SmartDato\DhlParcelReturns\Enums\LabelType;
use SmartDato\DhlParcelReturns\Enums\WeightUom;
use SmartDato\DhlParcelReturns\Facades\DhlParcelReturns;

$confirmation = DhlParcelReturns::orders()->create(
    data: new ReturnOrderData(
        receiverId: 'deu',
        shipper: new ContactAddressData(
            name1: 'Max Mustermann',
            addressStreet: 'Charles-de-Gaulle Str.',
            addressHouse: '20',
            postalCode: '53113',
            city: 'Bonn',
            email: 'max@mustermann.de',
        ),
        customerReference: 'Order #12345',
        itemWeight: new WeightData(WeightUom::Kilograms, 1),
        itemValue: new ValueData(value: 10, currency: Currency::Eur),
    ),
    labelType: LabelType::Both,
);

$confirmation->shipmentNo;        // "999991587211"
$confirmation->label->b64;        // base64-encoded shipment label
$confirmation->qrLabel?->b64;     // base64-encoded QR label (when requested)
$confirmation->qrLink;            // deep link to import the QR code into the Post & DHL app
$confirmation->routingCode;       // "40327653113+99000933090010"
```

### International returns with customs (CN23)

```php
use SmartDato\DhlParcelReturns\Data\Orders\CommodityData;
use SmartDato\DhlParcelReturns\Data\Orders\CustomsDetailsData;
use SmartDato\DhlParcelReturns\Enums\CountryOfOrigin;

$confirmation = DhlParcelReturns::orders()->create(
    data: new ReturnOrderData(
        receiverId: 'che',
        shipper: $shipper,
        itemWeight: new WeightData(WeightUom::Grams, 1200),
        itemValue: new ValueData(value: 1012.99),
        customsDetails: new CustomsDetailsData(
            items: [
                new CommodityData(
                    itemDescription: 'T-Shirt',
                    packagedQuantity: 1,
                    itemWeight: new WeightData(WeightUom::Grams, 500),
                    itemValue: new ValueData(value: 1000),
                    countryOfOrigin: CountryOfOrigin::FRA,
                    hsCode: '61099090',
                ),
            ],
        ),
    ),
);
```

### Look up return locations

```php
use SmartDato\DhlParcelReturns\Enums\Country;
use SmartDato\DhlParcelReturns\Facades\DhlParcelReturns;

$receivers = DhlParcelReturns::locations()->get(
    country: Country::Deu,
    postalCode: '53113',
    maxResult: 10,
);

foreach ($receivers as $receiver) {
    $receiver->receiverId;
    $receiver->billingNumber;
    $receiver->receiverAddress->city;
}
```

All filters are optional — call `->get()` with no arguments to list every receiver
configured for your account.

### API version (healthcheck)

```php
$version = DhlParcelReturns::general()->version();

$version->version; // "v1.0.0"
$version->env;     // "production"
```

This endpoint requires no authentication.

### Using without the Facade

You can create a `DhlParcelReturns` instance directly using `DhlParcelReturns::make()`.
This is useful when you prefer not to use the facade, need different credentials per
request, or want to support multi-tenant setups:

```php
use SmartDato\DhlParcelReturns\DhlParcelReturns;

// With OAuth2 (recommended)
$dhl = DhlParcelReturns::make([
    'api_key' => 'your-client-id',
    'client_secret' => 'your-client-secret',
    'username' => 'your-business-customer-username',
    'password' => 'your-business-customer-password',
    'sandbox' => true,
]);

// Or with legacy API key
$dhl = DhlParcelReturns::make([
    'api_key' => 'your-api-key',
    'sandbox' => true,
]);

$confirmation = $dhl->orders()->create($data);
```

You can also optionally pass a custom `base_url` if needed:

```php
$dhl = DhlParcelReturns::make([
    'api_key' => 'your-api-key',
    'base_url' => 'https://api-eu.dhl.com/parcel/de/shipping/returns/v1',
]);
```

## Label types

| Enum | Returns |
|------|---------|
| `LabelType::ShipmentLabel` | Shipment label only |
| `LabelType::QrLabel` | QR label only |
| `LabelType::Both` | Both documents (default) |

## Error handling

Any non-2xx response throws a `SmartDato\DhlParcelReturns\Exceptions\DhlParcelReturnsApiException`.
It parses the DHL RFC 7807 error body, exposing the `title`/`detail` as the message and
the HTTP status as the code:

```php
use SmartDato\DhlParcelReturns\Exceptions\DhlParcelReturnsApiException;

try {
    DhlParcelReturns::orders()->create($data);
} catch (DhlParcelReturnsApiException $e) {
    $e->getMessage();  // "Unprocessable: ..."
    $e->getCode();     // 422
    $e->detail;        // RFC 7807 detail
    $e->instance;      // RFC 7807 instance URI
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [SmartDato](https://github.com/smart-dato)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
