<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidPointException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PointTest extends TestCase
{
    #[DataProvider('provideValidPointStrings')]
    #[Test]
    public function parses_from_string(string $input, string $expectedOutput): void
    {
        $point = Point::fromString($input);
        $this->assertSame($expectedOutput, (string) $point);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideValidPointStrings(): iterable
    {
        yield 'basic point' => ['(1,2)', '(1,2)'];
        yield 'point with floats' => ['(1.5,2.5)', '(1.5,2.5)'];
        yield 'point with negative coordinates' => ['(-1.5,-2.5)', '(-1.5,-2.5)'];
        yield 'origin' => ['(0,0)', '(0,0)'];
        yield 'point with spaces' => ['( 1 , 2 )', '(1,2)'];
        yield 'high precision' => ['(45.123456789,179.987654321)', '(45.123456789,179.987654321)'];
        yield 'not a number' => ['(NaN,NaN)', '(NaN,NaN)'];
        yield 'positive and negative infinity' => ['(Infinity,-Infinity)', '(Infinity,-Infinity)'];
        yield 'short non-finite spellings' => ['(inf,-inf)', '(Infinity,-Infinity)'];
        yield 'lowercase non-finite spellings' => ['(nan,infinity)', '(NaN,Infinity)'];
        yield 'signed infinity' => ['(+inf,2)', '(Infinity,2)'];
    }

    #[Test]
    public function returns_correct_coordinates_via_getters(): void
    {
        $point = Point::fromString('(1.5,-2.5)');
        $this->assertSame(1.5, $point->getX());
        $this->assertSame(-2.5, $point->getY());
    }

    #[Test]
    public function is_constructed_with_float_values(): void
    {
        $point = new Point(1.5, -2.5);
        $this->assertSame(1.5, $point->getX());
        $this->assertSame(-2.5, $point->getY());
        $this->assertSame('(1.5,-2.5)', (string) $point);
    }

    #[DataProvider('provideInvalidPointStrings')]
    #[Test]
    public function throws_exception_for_invalid_format(string $value): void
    {
        $this->expectException(InvalidPointException::class);
        Point::fromString($value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideInvalidPointStrings(): iterable
    {
        yield 'empty string' => [''];
        yield 'plain text' => ['not a point'];
        yield 'missing parentheses' => ['1,2'];
        yield 'single value' => ['(1)'];
        yield 'too many values' => ['(1,2,3)'];
        yield 'embedded in text' => ['foo(1,2)bar'];
        yield 'leading text' => ['abc(1,2)'];
        yield 'trailing text' => ['(1,2)xyz'];
    }

    #[Test]
    public function accepts_non_finite_coordinates(): void
    {
        $this->assertSame('(NaN,1)', (string) new Point(\NAN, 1.0));
        $this->assertSame('(Infinity,-Infinity)', (string) new Point(\INF, -\INF));

        $point = Point::fromString('(NaN,-Infinity)');
        $this->assertNan($point->getX());
        $this->assertSame(-\INF, $point->getY());
    }

    #[Test]
    public function preserves_string_representation(): void
    {
        $point = new Point(1.0, 2.0);
        $this->assertSame('(1,2)', (string) $point);
    }

    #[Test]
    public function preserves_full_float_precision(): void
    {
        $coordinate = 0.12345678901234568;

        $point = new Point($coordinate, -$coordinate);

        $this->assertSame('(0.12345678901234568,-0.12345678901234568)', (string) $point);
        $this->assertSame($coordinate, Point::fromString((string) $point)->getX());
        $this->assertSame(-$coordinate, Point::fromString((string) $point)->getY());
    }

    #[Test]
    public function preserves_full_float_precision_under_a_comma_decimal_locale(): void
    {
        $originalLocale = \setlocale(\LC_NUMERIC, '0');
        if (\setlocale(\LC_NUMERIC, 'de_DE.UTF-8', 'de_DE', 'German_Germany', 'nl_NL.UTF-8') === false) {
            $this->markTestSkipped('No comma-decimal locale is installed on this machine');
        }

        // The full-precision fallback only runs when the short form does not round-trip, which needs a low precision.
        $originalPrecision = \ini_get('precision');
        \ini_set('precision', '3');

        try {
            $this->assertSame('(0.12345678901234568,1)', (string) new Point(0.12345678901234568, 1.0));
        } finally {
            \ini_set('precision', (string) $originalPrecision);
            \setlocale(\LC_NUMERIC, (string) $originalLocale);
        }
    }

    #[Test]
    public function accepts_high_precision_coordinates(): void
    {
        $point = new Point(45.123456789012, 179.987654321098);
        $this->assertSame(45.123456789012, $point->getX());
        $this->assertSame(179.987654321098, $point->getY());
    }
}
