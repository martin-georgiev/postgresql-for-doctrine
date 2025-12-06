<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsRange as TsRangeValueObject;

/**
 * Implementation of PostgreSQL TSRANGE type.
 *
 * @extends BaseRangeType<TsRangeValueObject>
 *
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class TsRange extends BaseRangeType
{
    protected const TYPE_NAME = Type::TSRANGE;

    protected function createFromString(string $value): Range
    {
        return TsRangeValueObject::fromString($value);
    }
}
