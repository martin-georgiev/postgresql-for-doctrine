<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Circle;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidCircleException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CircleTest extends TestCase
{
    #[DataProvider('provideValidCircleStrings')]
    #[Test]
    public function parses_from_string(string $value, string $expectedOutput): void
    {
        $circle = Circle::fromString($value);
        $this->assertSame($expectedOutput, (string) $circle);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideValidCircleStrings(): iterable
    {
        yield 'basic circle' => ['<(1,2),3>', '<(1,2),3>'];
        yield 'circle with floats' => ['<(1.5,2.5),3.5>', '<(1.5,2.5),3.5>'];
        yield 'circle with negative center' => ['<(-1,-2),5>', '<(-1,-2),5>'];
        yield 'circle at origin' => ['<(0,0),1>', '<(0,0),1>'];
        yield 'circle with float radius' => ['<(0,0),1.5>', '<(0,0),1.5>'];
        yield 'non-finite center' => ['<(NaN,-Infinity),3>', '<(NaN,-Infinity),3>'];
        yield 'non-finite radius' => ['<(1,2),Infinity>', '<(1,2),Infinity>'];
        yield 'short non-finite spellings' => ['<(nan,inf),nan>', '<(NaN,Infinity),NaN>'];
    }

    #[DataProvider('provideInvalidCircleStrings')]
    #[Test]
    public function throws_exception_for_invalid_format(string $value): void
    {
        $this->expectException(InvalidCircleException::class);
        Circle::fromString($value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideInvalidCircleStrings(): iterable
    {
        yield 'empty string' => [''];
        yield 'plain text' => ['not a circle'];
        yield 'missing angle brackets' => ['(1,2),3'];
        yield 'box format' => ['(1,2),(3,4)'];
        yield 'line format' => ['{1,2,3}'];
        yield 'only center, no radius' => ['<(1,2)>'];
        yield 'negative radius' => ['<(0,0),-1>'];
        yield 'negative infinite radius' => ['<(0,0),-Infinity>'];
    }

    #[Test]
    public function returns_correct_values_via_getters(): void
    {
        $circle = Circle::fromString('<(1.5,2.5),3.5>');
        $this->assertSame(1.5, $circle->getCenter()->getX());
        $this->assertSame(2.5, $circle->getCenter()->getY());
        $this->assertSame(3.5, $circle->getRadius());
    }

    #[Test]
    public function constructs_from_point_and_radius(): void
    {
        $circle = new Circle(new Point(1.0, 2.0), 3.0);
        $this->assertSame('<(1,2),3>', (string) $circle);
    }

    #[Test]
    public function throws_exception_for_negative_radius(): void
    {
        $this->expectException(InvalidCircleException::class);
        new Circle(new Point(0.0, 0.0), -1.0);
    }

    #[Test]
    public function accepts_non_finite_center_and_radius(): void
    {
        $circle = new Circle(new Point(\NAN, -\INF), \INF);

        $this->assertSame('<(NaN,-Infinity),Infinity>', (string) $circle);

        $parsed = Circle::fromString((string) $circle);
        $this->assertNan($parsed->getCenter()->getX());
        $this->assertSame(-\INF, $parsed->getCenter()->getY());
        $this->assertSame(\INF, $parsed->getRadius());
    }

    /**
     * PostgreSQL rejects every negative radius, -Infinity included, so the existing guard already matches it.
     */
    #[Test]
    public function throws_exception_for_negative_infinite_radius(): void
    {
        $this->expectException(InvalidCircleException::class);
        new Circle(new Point(0.0, 0.0), -\INF);
    }

    #[Test]
    public function preserves_full_float_precision(): void
    {
        $coordinate = 0.12345678901234568;

        $circle = new Circle(new Point($coordinate, -$coordinate), $coordinate);

        $this->assertSame('<(0.12345678901234568,-0.12345678901234568),0.12345678901234568>', (string) $circle);
        $this->assertSame($coordinate, Circle::fromString((string) $circle)->getRadius());
    }
}
