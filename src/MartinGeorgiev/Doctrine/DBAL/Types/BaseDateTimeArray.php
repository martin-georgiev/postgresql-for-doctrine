<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Traits\PostgresDateTimeConversionTrait;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateTimeInfinity;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Base class for PostgreSQL datetime array types (DATE[], TIMESTAMP[], TIMESTAMPTZ[]).
 *
 * Array items are \DateTimeImmutable or DateTimeInfinity instances.
 *
 * @see https://www.postgresql.org/docs/18/datatype-datetime.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseDateTimeArray extends BaseArray
{
    use PostgresDateTimeConversionTrait;

    abstract protected function throwInvalidPHPTypeException(mixed $item): never;

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        if ($item === null) {
            return true;
        }

        return $item instanceof \DateTimeInterface || $item instanceof DateTimeInfinity;
    }

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        if ($item instanceof DateTimeInfinity) {
            return '"'.$item->value.'"';
        }

        \assert($item instanceof \DateTimeInterface);

        return '"'.$this->transformDateTimeForPostgres($item).'"';
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray);
    }

    public function transformArrayItemForPHP(mixed $item): \DateTimeImmutable|DateTimeInfinity|null
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            $this->throwInvalidPHPTypeException($item);
        }

        return $this->transformPostgresStringForPHP($item);
    }
}
