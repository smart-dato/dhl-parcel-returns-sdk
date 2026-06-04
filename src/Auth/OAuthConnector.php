<?php

namespace SmartDato\DhlParcelReturns\Auth;

use Saloon\Http\Connector;
use Saloon\Http\Response;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use SmartDato\DhlParcelReturns\Exceptions\DhlParcelReturnsApiException;
use Throwable;

class OAuthConnector extends Connector
{
    use AcceptsJson;
    use AlwaysThrowOnErrors;

    public const string PRODUCTION_URL = 'https://api-eu.dhl.com';

    public const string SANDBOX_URL = 'https://api-sandbox.dhl.com';

    public function __construct(
        protected string $baseUrl = self::PRODUCTION_URL,
    ) {}

    public static function resolveUrl(?string $baseUrl, bool $sandbox = false): string
    {
        return $baseUrl ?? ($sandbox ? self::SANDBOX_URL : self::PRODUCTION_URL);
    }

    public function resolveBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getRequestException(Response $response, ?Throwable $senderException): ?Throwable
    {
        return DhlParcelReturnsApiException::fromResponse($response);
    }
}
