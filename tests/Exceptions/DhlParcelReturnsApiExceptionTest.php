<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use SmartDato\DhlParcelReturns\Auth\DhlParcelReturnsAuthenticator;
use SmartDato\DhlParcelReturns\Connectors\DhlParcelReturnsConnector;
use SmartDato\DhlParcelReturns\Exceptions\DhlParcelReturnsApiException;
use SmartDato\DhlParcelReturns\Requests\Locations\GetLocationsRequest;

function captureException(MockResponse $mockResponse): DhlParcelReturnsApiException
{
    $connector = new DhlParcelReturnsConnector(
        authenticator: new DhlParcelReturnsAuthenticator(apiKey: 'k', username: 'u', password: 'p'),
        baseUrl: DhlParcelReturnsConnector::SANDBOX_URL,
    );

    $connector->withMockClient(new MockClient([$mockResponse]));

    try {
        $connector->send(new GetLocationsRequest);
    } catch (DhlParcelReturnsApiException $exception) {
        return $exception;
    }

    throw new RuntimeException('Expected DhlParcelReturnsApiException to be thrown.');
}

it('extracts title and detail from an RFC 7807 error payload', function () {
    $exception = captureException(MockResponse::make(
        body: [
            'title' => 'Unauthorized',
            'status' => 401,
            'detail' => 'The credentials you provided are invalid.',
            'instance' => 'https://api.dhl.com/parcel/de/shipping/e0001.html',
        ],
        status: 401,
    ));

    expect($exception->getMessage())->toBe('Unauthorized: The credentials you provided are invalid.');
    expect($exception->detail)->toBe('The credentials you provided are invalid.');
    expect($exception->instance)->toBe('https://api.dhl.com/parcel/de/shipping/e0001.html');
    expect($exception->getCode())->toBe(401);
});

it('uses the title alone when no detail is present', function () {
    $exception = captureException(MockResponse::make(
        body: ['title' => 'Too Many Requests', 'status' => 429],
        status: 429,
    ));

    expect($exception->getMessage())->toBe('Too Many Requests');
    expect($exception->detail)->toBeNull();
    expect($exception->getCode())->toBe(429);
});

it('falls back to a generic message when the body is not JSON', function () {
    $exception = captureException(MockResponse::make(body: '<html>oops</html>', status: 502));

    expect($exception->getMessage())->toBe('DHL Parcel Returns API error: 502');
    expect($exception->detail)->toBeNull();
    expect($exception->getCode())->toBe(502);
});
