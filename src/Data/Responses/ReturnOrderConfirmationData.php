<?php

namespace SmartDato\DhlParcelReturns\Data\Responses;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class ReturnOrderConfirmationData extends Data
{
    public function __construct(
        public string $shipmentNo,
        public DocumentData $label,
        public string $routingCode,
        #[MapInputName('sstatus')]
        public JsonStatusData $status,
        public ?string $internationalShipmentNo = null,
        public ?DocumentData $qrLabel = null,
        public ?string $qrLink = null,
    ) {}
}
