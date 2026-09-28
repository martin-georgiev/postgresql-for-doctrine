<?php

declare(strict_types=1);

namespace MartinGeorgiev\Utils;

use MartinGeorgiev\Utils\PreciseFloatFormatter;

/**
 * The text PostgreSQL reads and writes for a float, its non-finite values included.
 *
 * @internal
 *
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class PostgresFloat
{
    /**
     * A float without its sign, for the operands PostgreSQL never accepts a negative value for, such as a radius.
     * The non-finite spellings (inf, infinity, nan) are part of the grammar; PostgreSQL emits them capitalized.
     *
     * @var string
     */
    public const UNSIGNED_PATTERN = '(?:(?:\d+(?:\.\d*)?|\.\d+)(?:[eE][+-]?\d+)?|(?i:inf(?:inity)?|nan))';

    /**
     * @var string
     */
    public const PATTERN = '[+-]?'.self::UNSIGNED_PATTERN;

    /**
     * Spells the non-finite values the way PostgreSQL emits them and keeps every digit of a finite one.
     */
    public static function format(float $value): string
    {
        if (\is_nan($value)) {
            return 'NaN';
        }

        if (\is_infinite($value)) {
            return $value > 0 ? 'Infinity' : '-Infinity';
        }

        return PreciseFloatFormatter::format($value);
    }

    /**
     * The spellings PostgreSQL accepts for a value outside the finite range. It emits `Infinity`, `-Infinity` and `NaN`,
     * but reads any case, the `inf` abbreviation and an explicit `+`.
     */
    public static function isNonFinite(string $value): bool
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
    public static function parse(string $value): float
    {
        return match (\mb_strtolower(\ltrim($value, '+'))) {
            'nan', '-nan' => \NAN,
            'inf', 'infinity' => \INF,
            '-inf', '-infinity' => -\INF,
            default => (float) $value,
        };
    }
}
