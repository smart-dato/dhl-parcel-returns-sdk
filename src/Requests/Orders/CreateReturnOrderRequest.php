<?php

namespace SmartDato\DhlParcelReturns\Requests\Orders;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use SmartDato\DhlParcelReturns\Data\Orders\ReturnOrderData;
use SmartDato\DhlParcelReturns\Data\Responses\ReturnOrderConfirmationData;
use SmartDato\DhlParcelReturns\Enums\LabelType;

class CreateReturnOrderRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected ReturnOrderData $data,
        protected ?LabelType $labelType = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/orders';
    }

    protected function defaultQuery(): array
    {
        return array_filter([
            'labelType' => $this->labelType?->value,
        ], static fn ($value) => $value !== null);
    }

    protected function defaultBody(): array
    {
        return $this->withoutNullValues($this->data->toArray());
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return array<array-key, mixed>
     */
    private function withoutNullValues(array $data): array
    {
        $filtered = [];

        foreach ($data as $key => $value) {
            if ($value === null) {
                continue;
            }

            $filtered[$key] = is_array($value) ? $this->withoutNullValues($value) : $value;
        }

        return $filtered;
    }

    public function createDtoFromResponse(Response $response): ReturnOrderConfirmationData
    {
        return ReturnOrderConfirmationData::from($response->json());
    }
}
