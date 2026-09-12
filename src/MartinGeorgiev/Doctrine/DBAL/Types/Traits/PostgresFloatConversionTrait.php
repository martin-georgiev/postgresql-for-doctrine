<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

/**
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
trait PostgresFloatConversionTrait
{
    /**
     * A float without its sign, for the operands PostgreSQL never accepts a negative value for, such as a radius.
     * The non-finite spellings (inf, infinity, nan) are part of the grammar; PostgreSQL emits them capitalized.
     *
     * @var string
     */
    protected const UNSIGNED_FLOAT_PATTERN = '(?:(?:\d+(?:\.\d*)?|\.\d+)(?:[eE][+-]?\d+)?|(?i:inf(?:inity)?|nan))';

    /**
     * @var string
     */
    protected const FLOAT_PATTERN = '[+-]?'.self::UNSIGNED_FLOAT_PATTERN;

    /**
     * Casting a float to string is bound by the `precision` ini setting (14 by default). This rewrites the value before
     * it reaches PostgreSQL. Fall back to the 17-digit form, which always round-trips, whenever the short one does not.
     */
    protected static function formatFloat(float $value): string
    {
        if (\is_nan($value)) {
            return 'NaN';
        }

        if (\is_infinite($value)) {
            return $value > 0 ? 'Infinity' : '-Infinity';
        }

        $shortForm = (string) $value;
        if ((float) $shortForm === $value) {
            return $shortForm;
        }

        return \sprintf('%.17H', $value);
    }

    /**
     * The spellings PostgreSQL accepts for a value outside the finite range. It emits `Infinity`, `-Infinity` and `NaN`,
     * but reads any case, the `inf` abbreviation and an explicit `+`.
     */
    protected static function isNonFiniteString(string $value): bool
    {
        return \in_array(
            \mb_strtolower($value),
            ['nan', '+nan', '-nan', 'inf', '+inf', '-inf', 'infinity', '+infinity', '-infinity'],
            true
        );
    }

    /**
     * Casting a string to float yields 0.0 for every non-finite spelling PostgreSQL uses. Those are matched explicitly.
     */
    protected static function parseFloat(string $value): float
    {
        return match (\mb_strtolower(\ltrim($value, '+'))) {
            'nan', '-nan' => \NAN,
            'inf', 'infinity' => \INF,
            '-inf', '-infinity' => -\INF,
            default => (float) $value,
        };
    }
}
