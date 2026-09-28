<?php

declare(strict_types=1);

namespace MartinGeorgiev\Utils;

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
     * Casting a float to string is bound by the `precision` ini setting (14 by default). This rewrites the value before
     * it reaches PostgreSQL. Fall back to the 17-digit form, which always round-trips, whenever the short one does not.
     */
    public static function format(float $value): string
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

    /**
     * Orders the magnitudes of two finite decimal numbers exactly, where comparing them as PHP floats rounds both first.
     *
     * @return int -1, 0 or 1
     */
    public static function compareMagnitudes(string $first, string $second): int
    {
        [$firstDigits, $firstExponent] = self::toSignificantDigits($first);
        [$secondDigits, $secondExponent] = self::toSignificantDigits($second);
        if ($firstDigits === '' || $secondDigits === '') {
            return ($firstDigits !== '') <=> ($secondDigits !== '');
        }

        $firstLeadingPosition = \strlen($firstDigits) + $firstExponent;
        $secondLeadingPosition = \strlen($secondDigits) + $secondExponent;
        if ($firstLeadingPosition !== $secondLeadingPosition) {
            return $firstLeadingPosition <=> $secondLeadingPosition;
        }

        $length = \max(\strlen($firstDigits), \strlen($secondDigits));

        return \strcmp(\str_pad($firstDigits, $length, '0'), \str_pad($secondDigits, $length, '0')) <=> 0;
    }

    /**
     * @return array{string, int} the digits without leading or trailing zeros, empty for zero, and the power of ten they are scaled by
     */
    private static function toSignificantDigits(string $value): array
    {
        \preg_match('/^[+-]?(\d*)(?:\.(\d*))?(?:[eE]([+-]?\d+))?\z/', $value, $matches);
        $fraction = $matches[2] ?? '';
        $digits = \ltrim(($matches[1] ?? '').$fraction, '0');
        $significantDigits = \rtrim($digits, '0');
        $exponent = (int) ($matches[3] ?? 0) - \strlen($fraction) + \strlen($digits) - \strlen($significantDigits);

        return [$significantDigits, $exponent];
    }
}
