<?php

namespace SmartDato\DhlParcelReturns\Data\Locations;

use SmartDato\DhlParcelReturns\Data\Orders\ContactAddressData;
use SmartDato\DhlParcelReturns\Enums\Country;
use Spatie\LaravelData\Data;

class ReceiverData extends Data
{
    public function __construct(
        public string $receiverId,
        public Country $shipperCountry,
        public string $ekp,
        public string $billingNumber,
        public string $companyName,
        public string $numberRange,
        public ContactAddressData $receiverAddress,
        public ContactAddressData $companyAddress,
        public ?string $contactEmail = null,
        public ?string $additionalEmailNote = null,
        public ?string $salesTaxIdentificationNumber = null,
        public ?string $court = null,
        public ?string $companyManagement = null,
    ) {}
}
