<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampForPHPException;

/**
 * Implementation of PostgreSQL TIMESTAMP (timestamp without time zone) data type.
 *
 * Unlike Doctrine's own datetime type, it reads and writes the infinity sentinels PostgreSQL stores, and it
 * returns \DateTimeImmutable rather than \DateTime.
 *
 * @see https://www.postgresql.org/docs/18/datatype-datetime.html
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Timestamp extends BaseDateTime
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::TIMESTAMP;

    protected function getPostgresFormat(): string
    {
        return 'Y-m-d H:i:s.u';
    }

    protected function getPHPFormats(): array
    {
        return ['X-m-d H:i:s.u', 'X-m-d H:i:s'];
    }

    protected function throwInvalidDatabaseTypeException(mixed $value): never
    {
        throw InvalidTimestampForDatabaseException::forInvalidType($value);
    }

    protected function throwInvalidPHPTypeException(mixed $value): never
    {
        throw InvalidTimestampForPHPException::forInvalidType($value);
    }

    protected function throwInvalidPHPFormatException(mixed $item): never
    {
        throw InvalidTimestampForPHPException::forInvalidFormat($item);
    }
}
