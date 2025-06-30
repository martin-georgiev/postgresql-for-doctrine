<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateRange as DateRangeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range;

/**
 * PostgreSQL DATERANGE type.
 *
 * @extends BaseRangeType<DateRangeValueObject>
 *
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class DateRange extends BaseRangeType
{
    protected const TYPE_NAME = 'daterange';

    protected function createFromString(string $value): Range
    {
        return DateRangeValueObject::fromString($value);
    }
}
