<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidPointException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PointTest extends TestCase
{
    #[DataProvider('provideValidPointStrings')]
    #[Test]
    public function can_create_from_string(string $input, string $expectedOutput): void
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
    }

    #[Test]
    public function can_return_correct_coordinates_via_getters(): void
    {
        $point = Point::fromString('(1.5,-2.5)');
        $this->assertSame(1.5, $point->getX());
        $this->assertSame(-2.5, $point->getY());
    }

    #[Test]
    public function can_be_constructed_with_float_values(): void
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
    public function throws_exception_for_nan_coordinate(): void
    {
        $this->expectException(InvalidPointException::class);
        new Point(\NAN, 1.0);
    }

    #[Test]
    public function throws_exception_for_infinite_coordinate(): void
    {
        $this->expectException(InvalidPointException::class);
        new Point(1.0, \INF);
    }

    #[Test]
    public function throws_exception_for_negative_infinite_coordinate(): void
    {
        $this->expectException(InvalidPointException::class);
        new Point(-\INF, 1.0);
    }

    #[Test]
    public function can_preserve_string_representation(): void
    {
        $point = new Point(1.0, 2.0);
        $this->assertSame('(1,2)', (string) $point);
    }

    #[Test]
    public function can_accept_high_precision_coordinates(): void
    {
        $point = new Point(45.123456789012, 179.987654321098);
        $this->assertSame(45.123456789012, $point->getX());
        $this->assertSame(179.987654321098, $point->getY());
    }
}
