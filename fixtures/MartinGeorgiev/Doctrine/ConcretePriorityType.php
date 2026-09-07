<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine;

use MartinGeorgiev\Doctrine\DBAL\Types\Enum;

final class ConcretePriorityType extends Enum
{
    protected const TYPE_NAME = 'test_priority';

    protected function getEnumClass(): string
    {
        return Priorities::class;
    }
}
