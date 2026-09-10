<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine;

use MartinGeorgiev\Doctrine\DBAL\Types\CompositeArray;

final class ConcreteInventoryItemArrayType extends CompositeArray
{
    protected const TYPE_NAME = 'test_inventory_item[]';

    protected function getCompositeClass(): string
    {
        return ConcreteInventoryItemType::class;
    }
}
