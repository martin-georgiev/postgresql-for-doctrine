<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Range as Int8RangeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range;

/**
 * Implementation of PostgreSQL INT8RANGE type.
 *
 * @extends BaseRangeType<Int8RangeValueObject>
 *
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Int8Range extends BaseRangeType
{
    protected const TYPE_NAME = Type::INT8RANGE;

    protected function createFromString(string $value): Range
    {
        return Int8RangeValueObject::fromString($value);
    }
}
