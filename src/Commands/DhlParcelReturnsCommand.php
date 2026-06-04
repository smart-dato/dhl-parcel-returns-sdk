<?php

namespace SmartDato\DhlParcelReturns\Commands;

use Illuminate\Console\Command;

class DhlParcelReturnsCommand extends Command
{
    public $signature = 'dhl-parcel-returns-sdk';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
