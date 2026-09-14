<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampTzForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampTzForPHPException;

/**
 * Implementation of PostgreSQL TIMESTAMPTZ (timestamp with time zone) data type.
 *
 * Unlike Doctrine's own datetimetz type, it reads and writes the infinity sentinels PostgreSQL stores, and it
 * returns \DateTimeImmutable rather than \DateTime.
 *
 * @see https://www.postgresql.org/docs/18/datatype-datetime.html
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class TimestampTz extends BaseDateTime
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::TIMESTAMPTZ;

    protected function getPostgresFormat(): string
    {
        return 'Y-m-d H:i:s.uP';
    }

    protected function getPHPFormats(): array
    {
        return ['X-m-d H:i:s.uP', 'X-m-d H:i:sP'];
    }

    protected function throwInvalidDatabaseTypeException(mixed $value): never
    {
        throw InvalidTimestampTzForDatabaseException::forInvalidType($value);
    }

    protected function throwInvalidPHPTypeException(mixed $value): never
    {
        throw InvalidTimestampTzForPHPException::forInvalidType($value);
    }

    protected function throwInvalidPHPFormatException(mixed $item): never
    {
        throw InvalidTimestampTzForPHPException::forInvalidFormat($item);
    }
}
