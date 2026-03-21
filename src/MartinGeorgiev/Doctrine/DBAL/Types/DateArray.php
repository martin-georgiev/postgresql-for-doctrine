<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateArrayItemForPHPException;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Implementation of PostgreSQL DATE[] data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-datetime.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class DateArray extends BaseArray
{
    protected const TYPE_NAME = Type::DATE_ARRAY;

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        if ($item === null) {
            return true;
        }

        return $item instanceof \DateTimeInterface;
    }

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        \assert($item instanceof \DateTimeInterface);

        return $item->format('Y-m-d');
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray);
    }

    public function transformArrayItemForPHP(mixed $item): ?\DateTimeImmutable
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            throw InvalidDateArrayItemForPHPException::forInvalidType($item);
        }

        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $item);
        if ($date === false) {
            throw InvalidDateArrayItemForPHPException::forInvalidFormat($item);
        }

        return $date->setTime(0, 0, 0);
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidDateArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidDateArrayItemForDatabaseException::forInvalidType($item);
    }
}
