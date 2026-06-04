<?php

namespace SmartDato\DhlParcelReturns\Data\Responses;

use Spatie\LaravelData\Data;

class JsonStatusData extends Data
{
    public function __construct(
        public string $title,
        public int $status,
        public ?string $type = null,
        public ?string $detail = null,
        public ?string $instance = null,
    ) {}
}
