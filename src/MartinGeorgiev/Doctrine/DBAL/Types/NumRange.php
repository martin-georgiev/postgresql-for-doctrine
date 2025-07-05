<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range;

/**
 * Implementation of PostgreSQL NUMRANGE type.
 *
 * @extends BaseRangeType<NumericRange>
 *
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class NumRange extends BaseRangeType
{
    protected const TYPE_NAME = 'numrange';

    protected function createFromString(string $value): Range
    {
        return NumericRange::fromString($value);
    }
}
