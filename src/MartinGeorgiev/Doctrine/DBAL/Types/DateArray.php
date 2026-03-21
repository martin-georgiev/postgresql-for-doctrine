<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateArrayItemForPHPException;

/**
 * Implementation of PostgreSQL DATE[] data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-datetime.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class DateArray extends BaseDateTimeArray
{
    protected const TYPE_NAME = Type::DATE_ARRAY;

    protected function getPostgresFormat(): string
    {
        return 'Y-m-d';
    }

    protected function getPHPFormats(): array
    {
        return ['Y-m-d'];
    }

    protected function transformParsedValueForPHP(\DateTimeImmutable $value): \DateTimeImmutable
    {
        return $value->setTime(0, 0, 0);
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidDateArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidDateArrayItemForDatabaseException::forInvalidType($item);
    }

    protected function throwInvalidPHPTypeException(mixed $item): never
    {
        throw InvalidDateArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwInvalidPHPFormatException(mixed $item): never
    {
        throw InvalidDateArrayItemForPHPException::forInvalidFormat($item);
    }
}
