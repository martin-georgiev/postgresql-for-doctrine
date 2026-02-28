<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Multirange as Int4MultirangeValueObject;

/**
 * Implementation of PostgreSQL INT4MULTIRANGE data type.
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 4.3
 *
 * @phpstan-extends BaseMultirangeType<Int4MultirangeValueObject>
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Int4Multirange extends BaseMultirangeType
{
    protected const TYPE_NAME = Type::INT4MULTIRANGE;

    protected function createFromString(string $value): Int4MultirangeValueObject
    {
        return Int4MultirangeValueObject::fromString($value);
    }
}
