<?php

namespace SmartDato\DhlParcelReturns\Resources;

use SmartDato\DhlParcelReturns\Data\Orders\ReturnOrderData;
use SmartDato\DhlParcelReturns\Data\Responses\ReturnOrderConfirmationData;
use SmartDato\DhlParcelReturns\Enums\LabelType;
use SmartDato\DhlParcelReturns\Requests\Orders\CreateReturnOrderRequest;

class OrdersResource extends BaseResource
{
    public function create(
        ReturnOrderData $data,
        ?LabelType $labelType = null,
    ): ReturnOrderConfirmationData {
        return $this->send(new CreateReturnOrderRequest(
            data: $data,
            labelType: $labelType,
        ))->dtoOrFail();
    }
}
