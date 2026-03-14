<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateMultirange as DateMultirangeValueObject;

/**
 * Implementation of PostgreSQL DATEMULTIRANGE data type.
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 4.4
 *
 * @phpstan-extends BaseMultirangeType<DateMultirangeValueObject>
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class DateMultirange extends BaseMultirangeType
{
    protected const TYPE_NAME = Type::DATEMULTIRANGE;

    protected function createFromString(string $value): DateMultirangeValueObject
    {
        return DateMultirangeValueObject::fromString($value);
    }
}
