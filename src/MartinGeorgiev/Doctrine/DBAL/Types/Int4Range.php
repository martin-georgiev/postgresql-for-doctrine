<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Range as Int4RangeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range;

/**
 * Implementation of PostgreSQL INT4RANGE type.
 *
 * @extends BaseRangeType<Int4RangeValueObject>
 *
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Int4Range extends BaseRangeType
{
    protected const TYPE_NAME = Type::INT4RANGE;

    protected function createFromString(string $value): Range
    {
        return Int4RangeValueObject::fromString($value);
    }
}
