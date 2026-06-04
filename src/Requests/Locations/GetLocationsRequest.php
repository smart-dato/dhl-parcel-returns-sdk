<?php

namespace SmartDato\DhlParcelReturns\Requests\Locations;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use SmartDato\DhlParcelReturns\Data\Locations\ReceiverData;
use SmartDato\DhlParcelReturns\Enums\Country;

class GetLocationsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected ?Country $countryCode = null,
        protected ?string $postalCode = null,
        protected ?string $receiverId = null,
        protected ?string $billingNumber = null,
        protected ?int $maxResult = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/locations';
    }

    protected function defaultQuery(): array
    {
        return array_filter([
            'countryCode' => $this->countryCode?->value,
            'postalCode' => $this->postalCode,
            'receiverId' => $this->receiverId,
            'billingNumber' => $this->billingNumber,
            'maxResult' => $this->maxResult,
        ], static fn ($value) => $value !== null);
    }

    /**
     * @return array<int, ReceiverData>
     */
    public function createDtoFromResponse(Response $response): array
    {
        return ReceiverData::collect($response->json());
    }
}
