<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidIntervalArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidIntervalArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Interval as IntervalValueObject;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Implementation of PostgreSQL INTERVAL[] data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-datetime.html#DATATYPE-INTERVAL-INPUT
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class IntervalArray extends BaseArray
{
    protected const TYPE_NAME = Type::INTERVAL_ARRAY;

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        if (\is_string($item)) {
            $item = IntervalValueObject::fromString($item);
        } elseif ($item instanceof \DateInterval) {
            $item = IntervalValueObject::fromDateInterval($item);
        }

        \assert($item instanceof IntervalValueObject);

        return '"'.$item.'"';
    }

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        if ($item === null || $item instanceof IntervalValueObject || $item instanceof \DateInterval) {
            return true;
        }

        if (\is_string($item)) {
            try {
                IntervalValueObject::fromString($item);

                return true;
            } catch (\Throwable) {
                return false;
            }
        }

        return false;
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray);
    }

    public function transformArrayItemForPHP(mixed $item): ?IntervalValueObject
    {
        if ($item === null) {
            return null;
        }

        if ($item instanceof IntervalValueObject) {
            return $item;
        }

        if (!\is_string($item)) {
            throw InvalidIntervalArrayItemForPHPException::forInvalidType($item);
        }

        try {
            return IntervalValueObject::fromString($item);
        } catch (\Throwable) {
            throw InvalidIntervalArrayItemForPHPException::forInvalidFormat($item);
        }
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidIntervalArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidIntervalArrayItemForDatabaseException::forInvalidFormat($item);
    }
}
