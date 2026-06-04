<?php

namespace SmartDato\DhlParcelReturns\Data\Orders;

use Spatie\LaravelData\Data;

class VasData extends Data
{
    public function __construct(
        public ?bool $goGreenPlus = null,
    ) {}
}
