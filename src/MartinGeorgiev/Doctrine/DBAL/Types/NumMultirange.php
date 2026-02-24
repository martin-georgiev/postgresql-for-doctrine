<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericMultirange as NumericMultirangeValueObject;

/**
 * Implementation of PostgreSQL NUMMULTIRANGE data type (PostgreSQL 14+).
 *
 * @see https://www.postgresql.org/docs/current/rangetypes.html
 * @since 4.3
 *
 * @phpstan-extends BaseMultirangeType<NumericMultirangeValueObject>
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class NumMultirange extends BaseMultirangeType
{
    protected const TYPE_NAME = Type::NUMMULTIRANGE;

    protected function createFromString(string $value): NumericMultirangeValueObject
    {
        return NumericMultirangeValueObject::fromString($value);
    }
}
