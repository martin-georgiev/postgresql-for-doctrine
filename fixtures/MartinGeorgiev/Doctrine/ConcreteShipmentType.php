<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine;

use Doctrine\DBAL\Types\Types;
use MartinGeorgiev\Doctrine\DBAL\Types\Composite;

final class ConcreteShipmentType extends Composite
{
    protected const TYPE_NAME = 'test_shipment';

    protected function getFieldTypes(): array
    {
        return [
            'label' => Types::TEXT,
            'item' => 'test_inventory_item',
            'packed_at' => Types::DATETIME_IMMUTABLE,
            'delivered_at' => Types::DATETIMETZ_IMMUTABLE,
            'dispatched_on' => Types::DATE_IMMUTABLE,
            'is_express' => Types::BOOLEAN,
            'tracking_id' => Types::GUID,
            'weight' => Types::FLOAT,
            'metadata' => Types::JSON,
        ];
    }
}
