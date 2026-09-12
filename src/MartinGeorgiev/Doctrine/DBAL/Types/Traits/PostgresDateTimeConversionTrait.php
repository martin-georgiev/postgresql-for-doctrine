<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateTimeInfinity;

/**
 * Converts between a PostgreSQL date, timestamp or timestamptz value and its PHP counterpart.
 *
 * Shared by the scalar types and by the items of their array counterparts, so both sides read and write
 * the same spellings: the infinity sentinels, the BC era and the years PostgreSQL prints with five digits.
 *
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
trait PostgresDateTimeConversionTrait
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

    abstract protected function throwInvalidPHPFormatException(mixed $item): never;

    protected function transformParsedValueForPHP(\DateTimeImmutable $value): \DateTimeImmutable
    {
        return $value;
    }

    /**
     * PostgreSQL has no year zero: it counts 1 BC where PHP counts year 0.
     * Every non-positive PHP year is mirrored around 1 and written in the BC era, which PostgreSQL marks with a suffix.
     */
    protected function transformDateTimeForPostgres(\DateTimeInterface $value): string
    {
        $yearToken = $value->format('Y');
        $year = (int) $yearToken;
        $formatted = $value->format($this->getPostgresFormat());
        if ($year > 0) {
            return $formatted;
        }

        return \sprintf('%04d', 1 - $year).\mb_substr($formatted, \mb_strlen($yearToken)).self::BC_ERA_SUFFIX;
    }

    protected function transformPostgresStringForPHP(string $value): \DateTimeImmutable|DateTimeInfinity
    {
        $infinity = DateTimeInfinity::tryFromString($value);
        if ($infinity instanceof DateTimeInfinity) {
            return $infinity;
        }

        $parsable = \str_ends_with($value, self::BC_ERA_SUFFIX)
            ? $this->transformBcEraYearToAstronomicalYear(\mb_substr($value, 0, -\mb_strlen(self::BC_ERA_SUFFIX)))
            : $value;

        $formats = $this->getPHPFormats();
        foreach ($formats as $format) {
            $parsed = \DateTimeImmutable::createFromFormat($format, $parsable);
            if ($parsed !== false) {
                return $this->transformParsedValueForPHP($parsed);
            }
        }

        $this->throwInvalidPHPFormatException($value);
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
