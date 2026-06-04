<?php

namespace SmartDato\DhlParcelReturns\Data\Orders;

use SmartDato\DhlParcelReturns\Enums\CountryOfOrigin;
use Spatie\LaravelData\Data;

class CommodityData extends Data
{
    public function __construct(
        public string $itemDescription,
        public int $packagedQuantity,
        public WeightData $itemWeight,
        public ValueData $itemValue,
        public ?CountryOfOrigin $countryOfOrigin = null,
        public ?string $hsCode = null,
    ) {}
}
