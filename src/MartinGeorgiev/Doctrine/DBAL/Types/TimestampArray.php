<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampArrayItemForPHPException;

/**
 * Implementation of PostgreSQL TIMESTAMP[] data type.
 *
 * @see https://www.postgresql.org/docs/current/datatype-datetime.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class TimestampArray extends BaseArray
{
    protected const TYPE_NAME = Type::TIMESTAMP_ARRAY;

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

        /** @var \DateTimeInterface $item */
        return $item->format('Y-m-d H:i:s.u');
    }

    public function transformArrayItemForPHP(mixed $item): ?\DateTimeImmutable
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            throw InvalidTimestampArrayItemForPHPException::forInvalidType($item);
        }

        $timestamp = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s.u', $item);
        if ($timestamp === false) {
            $timestamp = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $item);
        }

        if ($timestamp === false) {
            throw InvalidTimestampArrayItemForPHPException::forInvalidFormat($item);
        }

        return $timestamp;
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw new \InvalidArgumentException(
            \sprintf('Given PHP value content type is not PHP array. Instead it is "%s".', \gettype($value))
        );
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidTimestampArrayItemForDatabaseException::forInvalidType($item);
    }
}
