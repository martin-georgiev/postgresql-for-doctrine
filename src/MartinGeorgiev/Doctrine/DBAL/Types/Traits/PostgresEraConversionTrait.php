<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

/**
 * PostgreSQL writes a date before year 1 in the BC era, with a suffix, and a year past 9999 with as many digits as it needs.
 * PHP counts the first in astronomical years, with a year zero, and needs a sign to read the second.
 *
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
trait PostgresEraConversionTrait
{
    /**
     * @var string
     */
    private const BC_ERA_SUFFIX = ' BC';

    /**
     * PostgreSQL has no year zero: it counts 1 BC where PHP counts year 0.
     * Every non-positive PHP year is mirrored around 1 and written in the BC era, which PostgreSQL marks with a suffix.
     *
     * @param string $format opens with the year, which is rewritten on its own for values in the BC era
     */
    protected static function formatInPostgresEra(\DateTimeInterface $value, string $format): string
    {
        $yearToken = $value->format('Y');
        $year = (int) $yearToken;
        $formatted = $value->format($format);
        if ($year > 0) {
            return $formatted;
        }

        return \sprintf('%04d', 1 - $year).\mb_substr($formatted, \mb_strlen($yearToken)).self::BC_ERA_SUFFIX;
    }

    /**
     * Rewriting the era in the string keeps 29 February of a BC leap year intact.
     * PHP counts it in the astronomical year, which is a leap year, while the BC year number PostgreSQL prints for it is not.
     */
    protected static function moveBcEraToAstronomicalYear(string $value): string
    {
        if (!\str_ends_with($value, self::BC_ERA_SUFFIX)) {
            return $value;
        }

        $valueWithoutEra = \mb_substr($value, 0, -\mb_strlen(self::BC_ERA_SUFFIX));
        if (\preg_match('/^(\d+)(.*)\z/s', $valueWithoutEra, $matches) !== 1) {
            return $valueWithoutEra;
        }

        return \sprintf('%+05d', 1 - (int) $matches[1]).$matches[2];
    }

    /**
     * PHP's free-form date parser reads a signed year of any length, but takes a bare five-digit year for a date
     * and a time, and the BC suffix for a time zone.
     */
    protected static function signYearForPhpParser(string $value): string
    {
        $astronomicalValue = self::moveBcEraToAstronomicalYear($value);
        if ($astronomicalValue !== $value) {
            return $astronomicalValue;
        }

        return \preg_match('/^\d{5,}-/', $value) === 1 ? '+'.$value : $value;
    }
}
