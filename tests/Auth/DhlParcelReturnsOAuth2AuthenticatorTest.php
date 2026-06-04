<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Response;
use SmartDato\DhlParcelReturns\Auth\DhlParcelReturnsAuthenticator;
use SmartDato\DhlParcelReturns\Auth\OAuthConnector;
use SmartDato\DhlParcelReturns\Auth\Requests\GetAccessTokenRequest;
use SmartDato\DhlParcelReturns\Connectors\DhlParcelReturnsConnector;
use SmartDato\DhlParcelReturns\Exceptions\DhlParcelReturnsApiException;
use SmartDato\DhlParcelReturns\Requests\Locations\GetLocationsRequest;

afterEach(function () {
    MockClient::destroyGlobal();
});

function buildOAuth2Authenticator(): DhlParcelReturnsAuthenticator
{
    return new DhlParcelReturnsAuthenticator(
        apiKey: 'test-client-id',
        username: 'user-valid',
        password: 'SandboxPasswort2023!',
        clientSecret: 'test-client-secret',
        oauthBaseUrl: OAuthConnector::SANDBOX_URL,
    );
}

function sendLocationsRequest(DhlParcelReturnsAuthenticator $authenticator): Response
{
    $connector = new DhlParcelReturnsConnector(
        authenticator: $authenticator,
        baseUrl: DhlParcelReturnsConnector::SANDBOX_URL,
    );

    return $connector->send(new GetLocationsRequest);
}

it('exchanges credentials for an access token and sends Bearer auth on subsequent calls', function () {
    $mockClient = MockClient::global([
        GetAccessTokenRequest::class => MockResponse::make([
            'access_token' => 'oauth-access-token',
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ]),
        GetLocationsRequest::class => MockResponse::make([]),
    ]);

    sendLocationsRequest(buildOAuth2Authenticator());

    $tokenRequest = $mockClient->getRecordedResponses()[0]->getPendingRequest();
    expect($tokenRequest->getRequest())->toBeInstanceOf(GetAccessTokenRequest::class);
    expect($tokenRequest->getUrl())->toBe(OAuthConnector::SANDBOX_URL.'/parcel/de/account/auth/ropc/v1/token');

    $tokenBody = $tokenRequest->body()->all();
    expect($tokenBody)->toBe([
        'grant_type' => 'password',
        'client_id' => 'test-client-id',
        'client_secret' => 'test-client-secret',
        'username' => 'user-valid',
        'password' => 'SandboxPasswort2023!',
    ]);

    $locationsRequest = $mockClient->getRecordedResponses()[1]->getPendingRequest();
    expect($locationsRequest->headers()->all())->toHaveKey('Authorization', 'Bearer oauth-access-token');
    expect($locationsRequest->headers()->all())->not->toHaveKey('dhl-api-key');
});

it('caches the access token across multiple requests', function () {
    $mockClient = MockClient::global([
        GetAccessTokenRequest::class => MockResponse::make([
            'access_token' => 'oauth-access-token',
            'expires_in' => 3600,
        ]),
        GetLocationsRequest::class => MockResponse::make([]),
    ]);

    $authenticator = buildOAuth2Authenticator();

    sendLocationsRequest($authenticator);
    sendLocationsRequest($authenticator);

    $tokenCalls = array_filter(
        $mockClient->getRecordedResponses(),
        fn ($recorded) => $recorded->getPendingRequest()->getRequest() instanceof GetAccessTokenRequest,
    );

    expect($tokenCalls)->toHaveCount(1);
});

it('falls back to legacy auth when client_secret is not provided', function () {
    MockClient::global([
        GetLocationsRequest::class => MockResponse::make([]),
    ]);

    $authenticator = new DhlParcelReturnsAuthenticator(
        apiKey: 'legacy-api-key',
        username: 'user-valid',
        password: 'SandboxPasswort2023!',
        oauthBaseUrl: OAuthConnector::SANDBOX_URL,
    );

    $response = sendLocationsRequest($authenticator);
    $headers = $response->getPendingRequest()->headers()->all();

    expect($headers)->toHaveKey('dhl-api-key', 'legacy-api-key');
    expect($headers)->toHaveKey('Authorization', 'Basic '.base64_encode('user-valid:SandboxPasswort2023!'));
});

it('throws when the token endpoint response is missing access_token', function () {
    MockClient::global([
        GetAccessTokenRequest::class => MockResponse::make(['error' => 'invalid_grant']),
    ]);

    expect(fn () => sendLocationsRequest(buildOAuth2Authenticator()))
        ->toThrow(DhlParcelReturnsApiException::class, 'missing a valid access_token');
});
