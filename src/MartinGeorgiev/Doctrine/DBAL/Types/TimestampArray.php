<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampArrayItemForPHPException;

/**
 * Implementation of PostgreSQL TIMESTAMP[] data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-datetime.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class TimestampArray extends BaseDateTimeArray
{
    protected const TYPE_NAME = Type::TIMESTAMP_ARRAY;

    protected function getPostgresFormat(): string
    {
        return 'Y-m-d H:i:s.u';
    }

    protected function getPhpFormats(): array
    {
        return ['Y-m-d H:i:s.u', 'Y-m-d H:i:s'];
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidTimestampArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidTimestampArrayItemForDatabaseException::forInvalidType($item);
    }

    protected function throwInvalidPhpTypeException(mixed $item): never
    {
        throw InvalidTimestampArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwInvalidPhpFormatException(mixed $item): never
    {
        throw InvalidTimestampArrayItemForPHPException::forInvalidFormat($item);
    }
}
