<?php

namespace SmartDato\DhlParcelReturns\Data\Responses;

use Spatie\LaravelData\Data;

class DocumentData extends Data
{
    public function __construct(
        public ?string $b64 = null,
    ) {}
}
