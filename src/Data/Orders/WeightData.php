<?php

namespace SmartDato\DhlParcelReturns\Data\Orders;

use SmartDato\DhlParcelReturns\Enums\WeightUom;
use Spatie\LaravelData\Data;

class WeightData extends Data
{
    public function __construct(
        public WeightUom $uom,
        public float $value,
    ) {}
}
