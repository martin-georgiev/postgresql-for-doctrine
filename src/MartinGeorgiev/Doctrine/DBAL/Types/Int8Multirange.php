<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Multirange as Int8MultirangeValueObject;

/**
 * Implementation of PostgreSQL INT8MULTIRANGE data type.
 *
 * @see https://www.postgresql.org/docs/current/rangetypes.html
 * @since 4.3
 *
 * @phpstan-extends BaseMultirangeType<Int8MultirangeValueObject>
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Int8Multirange extends BaseMultirangeType
{
    protected const TYPE_NAME = Type::INT8MULTIRANGE;

    protected function createFromString(string $value): Int8MultirangeValueObject
    {
        return Int8MultirangeValueObject::fromString($value);
    }
}
