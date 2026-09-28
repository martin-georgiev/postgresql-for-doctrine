<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Utils;

use MartinGeorgiev\Utils\PostgresFloat;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PostgresFloatTest extends TestCase
{
    #[DataProvider('provideFormattedFloats')]
    #[Test]
    public function formats_a_float_the_way_postgres_reads_it(float $value, string $expected): void
    {
        $this->assertSame($expected, PostgresFloat::format($value));
    }

    /**
     * @return array<string, array{float, string}>
     */
    public static function provideFormattedFloats(): array
    {
        return [
            'short form that reads back as the same float' => [1.5, '1.5'],
            'whole number' => [1.0, '1'],
            '17 significant digits' => [0.30000000000000004, '0.30000000000000004'],
            'not a number' => [\NAN, 'NaN'],
            'positive infinity' => [\INF, 'Infinity'],
            'negative infinity' => [-\INF, '-Infinity'],
        ];
    }

    #[DataProvider('provideParsedSpellings')]
    #[Test]
    public function parses_every_spelling_postgres_reads(string $value, float $expected): void
    {
        $this->assertSame($expected, PostgresFloat::parse($value));
    }

    /**
     * @return array<string, array{string, float}>
     */
    public static function provideParsedSpellings(): array
    {
        return [
            'finite value' => ['1.5', 1.5],
            'canonical positive infinity' => ['Infinity', \INF],
            'abbreviated positive infinity with an explicit sign' => ['+inf', \INF],
            'canonical negative infinity' => ['-Infinity', -\INF],
            'abbreviated negative infinity' => ['-inf', -\INF],
        ];
    }

    #[DataProvider('provideNotANumberSpellings')]
    #[Test]
    public function parses_every_not_a_number_spelling(string $value): void
    {
        $this->assertNan(PostgresFloat::parse($value));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideNotANumberSpellings(): array
    {
        return [
            'canonical' => ['NaN'],
            'lowercase' => ['nan'],
            'negative' => ['-nan'],
        ];
    }

    #[DataProvider('provideNonFiniteCandidates')]
    #[Test]
    public function validates_non_finite_spellings(string $value, bool $isNonFinite): void
    {
        $this->assertSame($isNonFinite, PostgresFloat::isNonFinite($value));
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public static function provideNonFiniteCandidates(): array
    {
        return [
            'canonical positive infinity' => ['Infinity', true],
            'uppercase abbreviation' => ['INF', true],
            'explicitly positive not a number' => ['+nan', true],
            'finite value' => ['1.5', false],
            'a word that only starts like infinity' => ['infinite', false],
        ];
    }

    #[DataProvider('provideMagnitudesToCompare')]
    #[Test]
    public function compares_decimal_magnitudes_exactly(string $first, string $second, int $expected): void
    {
        $this->assertSame($expected, PostgresFloat::compareMagnitudes($first, $second));
    }

    /**
     * @return array<string, array{string, string, int}>
     */
    public static function provideMagnitudesToCompare(): array
    {
        return [
            'equal' => ['1.5', '1.5', 0],
            'equal with trailing zeros and an exponent' => ['1.50', '15E-1', 0],
            'the sign is ignored' => ['-3', '2', 1],
            'more integer digits' => ['10', '9', 1],
            'fewer integer digits' => ['9', '10', -1],
            'zero spelled two ways' => ['0', '-0.00', 0],
            'zero below any non-zero value' => ['0', '1E-400', -1],
            'non-zero value above zero' => ['1E-400', '0', 1],
            'differs only past what a double holds' => ['340282356779733661637539395458142568447', '3.40282356779733661637539395458142568448E+38', -1],
            'leading zeros in the fraction' => ['0.0005', '5E-4', 0],
        ];
    }
}
