<?php

namespace SmartDato\DhlParcelReturns\Requests\General;

use Saloon\Contracts\Authenticator;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use SmartDato\DhlParcelReturns\Auth\NullAuthenticator;
use SmartDato\DhlParcelReturns\Data\Responses\VersionData;

class GetVersionRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '';
    }

    protected function defaultAuth(): ?Authenticator
    {
        return new NullAuthenticator;
    }

    public function createDtoFromResponse(Response $response): VersionData
    {
        return VersionData::from($response->json('amp') ?? $response->json());
    }
}
