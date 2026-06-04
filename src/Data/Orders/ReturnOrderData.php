<?php

namespace SmartDato\DhlParcelReturns\Data\Orders;

use Spatie\LaravelData\Data;

class ReturnOrderData extends Data
{
    public function __construct(
        public string $receiverId,
        public ContactAddressData $shipper,
        public ?string $customerReference = null,
        public ?string $shipmentReference = null,
        public ?string $creationSoftware = null,
        public ?WeightData $itemWeight = null,
        public ?ValueData $itemValue = null,
        public ?CustomsDetailsData $customsDetails = null,
        public ?VasData $services = null,
    ) {}
}
