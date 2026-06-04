<?php

namespace SmartDato\DhlParcelReturns\Enums;

enum LabelType: string
{
    case ShipmentLabel = 'SHIPMENT_LABEL';
    case QrLabel = 'QR_LABEL';
    case Both = 'BOTH';
}
