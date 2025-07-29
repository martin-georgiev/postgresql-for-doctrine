<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzRange as TstzRangeValueObject;

/**
 * Implementation of PostgreSQL TSTZRANGE type.
 *
 * @extends BaseRangeType<TstzRangeValueObject>
 *
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class TstzRange extends BaseRangeType
{
    protected const TYPE_NAME = 'tstzrange';

    protected function createFromString(string $value): Range
    {
        return TstzRangeValueObject::fromString($value);
    }
}
