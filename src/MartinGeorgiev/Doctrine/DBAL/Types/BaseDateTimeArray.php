<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Traits\PostgresEraConversionTrait;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateTimeInfinity;

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
    use PostgresEraConversionTrait;

    /**
     * Returns the format string for serializing to PostgreSQL.
     *
     * It must open with the year, which is rewritten on its own for values in the BC era.
     */
    abstract protected function getPostgresFormat(): string;

    /**
     * Returns format strings for parsing from PostgreSQL, tried in order.
     *
     * To support the five-digit and non-positive years PostgreSQL emits, they use X rather than Y for the year format.
     *
     * @return non-empty-list<string>
     */
    abstract protected function getPHPFormats(): array;

    protected function transformParsedValueForPHP(\DateTimeImmutable $value): \DateTimeImmutable
    {
        return $value;
    }

    abstract protected function throwInvalidPHPTypeException(mixed $item): never;

    abstract protected function throwInvalidPHPFormatException(mixed $item): never;

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

        return '"'.self::formatInPostgresEra($item, $this->getPostgresFormat()).'"';
    }

    public function transformArrayItemForPHP(mixed $item): \DateTimeImmutable|DateTimeInfinity|null
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            $this->throwInvalidPHPTypeException($item);
        }

        $infinity = DateTimeInfinity::tryFromString($item);
        if ($infinity instanceof DateTimeInfinity) {
            return $infinity;
        }

        $parsable = self::moveBcEraToAstronomicalYear($item);

        foreach ($this->getPHPFormats() as $format) {
            $parsed = \DateTimeImmutable::createFromFormat($format, $parsable);
            if ($parsed !== false) {
                return $this->transformParsedValueForPHP($parsed);
            }
        }

        $this->throwInvalidPHPFormatException($item);
    }
}
