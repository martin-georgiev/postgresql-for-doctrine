<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine;

use MartinGeorgiev\Doctrine\DBAL\Types\Enum;

final class ConcreteShadeType extends Enum
{
    protected const TYPE_NAME = 'test.test_shade';

    protected function getEnumClass(): string
    {
        return Shades::class;
    }
}
