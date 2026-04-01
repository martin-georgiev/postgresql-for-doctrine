<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampTzArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampTzArrayItemForPHPException;

/**
 * Implementation of PostgreSQL TIMESTAMPTZ[] data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-datetime.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class TimestampTzArray extends BaseDateTimeArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::TIMESTAMPTZ_ARRAY;

    protected function getPostgresFormat(): string
    {
        return 'Y-m-d H:i:s.uP';
    }

    protected function getPHPFormats(): array
    {
        return ['Y-m-d H:i:s.uP', 'Y-m-d H:i:sP'];
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidTimestampTzArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidTimestampTzArrayItemForDatabaseException::forInvalidType($item);
    }

    protected function throwInvalidPHPTypeException(mixed $item): never
    {
        throw InvalidTimestampTzArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwInvalidPHPFormatException(mixed $item): never
    {
        throw InvalidTimestampTzArrayItemForPHPException::forInvalidFormat($item);
    }
}
