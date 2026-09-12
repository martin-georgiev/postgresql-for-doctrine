<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

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
    /**
     * @var string
     */
    private const BC_ERA_SUFFIX = ' BC';

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

        return '"'.$this->transformDateTimeForPostgres($item).'"';
    }

    /**
     * PostgreSQL has no year zero: it counts 1 BC where PHP counts year 0.
     * Every non-positive PHP year is mirrored around 1 and written in the BC era, which PostgreSQL marks with a suffix.
     */
    private function transformDateTimeForPostgres(\DateTimeInterface $item): string
    {
        $yearToken = $item->format('Y');
        $year = (int) $yearToken;
        $formatted = $item->format($this->getPostgresFormat());
        if ($year > 0) {
            return $formatted;
        }

        return \sprintf('%04d', 1 - $year).\mb_substr($formatted, \mb_strlen($yearToken)).self::BC_ERA_SUFFIX;
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

        $infinity = DateTimeInfinity::tryFromString($item);
        if ($infinity instanceof DateTimeInfinity) {
            return $infinity;
        }

        $parsable = \str_ends_with($item, self::BC_ERA_SUFFIX)
            ? $this->transformBcEraYearToAstronomicalYear(\mb_substr($item, 0, -\mb_strlen(self::BC_ERA_SUFFIX)))
            : $item;

        foreach ($this->getPHPFormats() as $format) {
            $parsed = \DateTimeImmutable::createFromFormat($format, $parsable);
            if ($parsed !== false) {
                return $this->transformParsedValueForPHP($parsed);
            }
        }

        $this->throwInvalidPHPFormatException($item);
    }

    /**
     * Rewriting the era in the string keeps 29 February of a BC leap year intact.
     * PHP counts it in the astronomical year, which is a leap year, while the BC year number PostgreSQL prints for it is not.
     */
    private function transformBcEraYearToAstronomicalYear(string $value): string
    {
        if (\preg_match('/^(\d+)(.*)\z/s', $value, $matches) !== 1) {
            return $value;
        }

        return \sprintf('%+05d', 1 - (int) $matches[1]).$matches[2];
    }
}
