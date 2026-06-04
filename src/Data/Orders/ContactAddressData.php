<?php

namespace SmartDato\DhlParcelReturns\Data\Orders;

use Spatie\LaravelData\Data;

class ContactAddressData extends Data
{
    public function __construct(
        public string $name1,
        public string $addressStreet,
        public string $addressHouse,
        public string $postalCode,
        public string $city,
        public ?string $name2 = null,
        public ?string $name3 = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $state = null,
    ) {}
}
