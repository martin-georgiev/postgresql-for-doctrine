<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzMultirange as TstzMultirangeValueObject;

/**
 * Implementation of PostgreSQL TSTZMULTIRANGE data type.
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 4.4
 *
 * @phpstan-extends BaseMultirangeType<TstzMultirangeValueObject>
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class TstzMultirange extends BaseMultirangeType
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::TSTZMULTIRANGE;

    protected function createFromString(string $value): TstzMultirangeValueObject
    {
        return TstzMultirangeValueObject::fromString($value);
    }
}
