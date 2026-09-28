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
}
