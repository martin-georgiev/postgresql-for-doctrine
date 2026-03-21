<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampTzArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampTzArrayItemForPHPException;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Implementation of PostgreSQL TIMESTAMPTZ[] data type.
 *
 * @see https://www.postgresql.org/docs/current/datatype-datetime.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class TimestampTzArray extends BaseArray
{
    protected const TYPE_NAME = Type::TIMESTAMPTZ_ARRAY;

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

        return '"'.$item->format('Y-m-d H:i:s.uP').'"';
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
            throw InvalidTimestampTzArrayItemForPHPException::forInvalidType($item);
        }

        $timestamp = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s.uP', $item);
        if ($timestamp === false) {
            $timestamp = \DateTimeImmutable::createFromFormat('Y-m-d H:i:sP', $item);
        }

        if ($timestamp === false) {
            throw InvalidTimestampTzArrayItemForPHPException::forInvalidFormat($item);
        }

        return $timestamp;
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidTimestampTzArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidTimestampTzArrayItemForDatabaseException::forInvalidType($item);
    }
}
