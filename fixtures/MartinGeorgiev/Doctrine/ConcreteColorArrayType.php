<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine;

use MartinGeorgiev\Doctrine\DBAL\Types\EnumArray;

final class ConcreteColorArrayType extends EnumArray
{
    protected const TYPE_NAME = 'test_color[]';

    protected function getEnumClass(): string
    {
        return Colors::class;
    }
}
