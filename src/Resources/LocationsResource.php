<?php

namespace SmartDato\DhlParcelReturns\Resources;

use SmartDato\DhlParcelReturns\Data\Locations\ReceiverData;
use SmartDato\DhlParcelReturns\Enums\Country;
use SmartDato\DhlParcelReturns\Requests\Locations\GetLocationsRequest;

class LocationsResource extends BaseResource
{
    /**
     * @return array<int, ReceiverData>
     */
    public function get(
        ?Country $country = null,
        ?string $postalCode = null,
        ?string $receiverId = null,
        ?string $billingNumber = null,
        ?int $maxResult = null,
    ): array {
        return $this->send(new GetLocationsRequest(
            countryCode: $country,
            postalCode: $postalCode,
            receiverId: $receiverId,
            billingNumber: $billingNumber,
            maxResult: $maxResult,
        ))->dtoOrFail();
    }
}
