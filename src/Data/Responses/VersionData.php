<?php

namespace SmartDato\DhlParcelReturns\Data\Responses;

use Spatie\LaravelData\Data;

class VersionData extends Data
{
    public function __construct(
        public ?string $name = null,
        public ?string $version = null,
        public ?string $rev = null,
        public ?string $env = null,
    ) {}
}
