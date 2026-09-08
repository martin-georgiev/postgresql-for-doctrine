<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine;

use Doctrine\DBAL\Types\Types;
use MartinGeorgiev\Doctrine\DBAL\Types\Composite;

final class ConcreteInventoryItemType extends Composite
{
    protected const TYPE_NAME = 'test_inventory_item';

    protected function getFieldTypes(): array
    {
        return [
            'name' => Types::TEXT,
            'supplier_id' => Types::INTEGER,
            'price' => Types::DECIMAL,
        ];
    }
}
