<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateForPHPException;

/**
 * Implementation of PostgreSQL DATE data type.
 *
 * Unlike Doctrine's own date type, it reads and writes the infinity sentinels PostgreSQL stores, and it returns
 * \DateTimeImmutable rather than \DateTime. Registering it replaces the built-in date type for the whole
 * application, so it is an explicit opt-in. See AVAILABLE-TYPES.md for the trade-off.
 *
 * @see https://www.postgresql.org/docs/18/datatype-datetime.html
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Date extends BaseDateTime
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::DATE;

    protected function getPostgresFormat(): string
    {
        return 'Y-m-d';
    }

    protected function getPHPFormats(): array
    {
        return ['X-m-d'];
    }

    protected function transformParsedValueForPHP(\DateTimeImmutable $value): \DateTimeImmutable
    {
        return $value->setTime(0, 0, 0);
    }

    protected function throwInvalidDatabaseTypeException(mixed $value): never
    {
        throw InvalidDateForDatabaseException::forInvalidType($value);
    }

    protected function throwInvalidPHPTypeException(mixed $value): never
    {
        throw InvalidDateForPHPException::forInvalidType($value);
    }

    protected function throwInvalidPHPFormatException(mixed $item): never
    {
        throw InvalidDateForPHPException::forInvalidFormat($item);
    }
}
