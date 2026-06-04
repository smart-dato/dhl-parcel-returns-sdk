<?php

namespace SmartDato\DhlParcelReturns\Data\Orders;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class CustomsDetailsData extends Data
{
    /**
     * @param  array<int, CommodityData>  $items
     */
    public function __construct(
        #[DataCollectionOf(CommodityData::class)]
        public array $items,
    ) {}
}
