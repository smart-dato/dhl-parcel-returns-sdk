<?php

namespace SmartDato\DhlParcelReturns\Resources;

use SmartDato\DhlParcelReturns\Data\Responses\VersionData;
use SmartDato\DhlParcelReturns\Requests\General\GetVersionRequest;

class GeneralResource extends BaseResource
{
    public function version(): VersionData
    {
        return $this->send(new GetVersionRequest)->dtoOrFail();
    }
}
