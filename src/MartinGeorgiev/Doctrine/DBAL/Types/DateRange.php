<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateRange as DateRangeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range;

/**
 * Implementation of PostgreSQL DATERANGE type.
 *
 * @extends BaseRangeType<DateRangeValueObject>
 *
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class DateRange extends BaseRangeType
{
    protected const TYPE_NAME = Type::DATERANGE;

    protected function createFromString(string $value): Range
    {
        return DateRangeValueObject::fromString($value);
    }
}
