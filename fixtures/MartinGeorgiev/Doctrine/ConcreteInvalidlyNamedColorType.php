<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine;

use MartinGeorgiev\Doctrine\DBAL\Types\Enum;

final class ConcreteInvalidlyNamedColorType extends Enum
{
    protected const TYPE_NAME = 'test_color"; DROP TABLE users; --';

    protected function getEnumClass(): string
    {
        return Colors::class;
    }
}
