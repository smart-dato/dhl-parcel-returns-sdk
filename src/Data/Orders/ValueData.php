<?php

namespace SmartDato\DhlParcelReturns\Data\Orders;

use SmartDato\DhlParcelReturns\Enums\Currency;
use Spatie\LaravelData\Data;

class ValueData extends Data
{
    public function __construct(
        public float $value,
        public Currency $currency = Currency::Eur,
    ) {}
}
