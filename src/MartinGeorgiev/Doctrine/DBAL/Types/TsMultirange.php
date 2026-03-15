<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsMultirange as TsMultirangeValueObject;

/**
 * Implementation of PostgreSQL TSMULTIRANGE data type.
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 4.4
 *
 * @phpstan-extends BaseMultirangeType<TsMultirangeValueObject>
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class TsMultirange extends BaseMultirangeType
{
    protected const TYPE_NAME = Type::TSMULTIRANGE;

    protected function createFromString(string $value): TsMultirangeValueObject
    {
        return TsMultirangeValueObject::fromString($value);
    }
}
