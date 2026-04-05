<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidPolygonException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Polygon;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PolygonTest extends TestCase
{
    #[DataProvider('provideValidPolygonStrings')]
    #[Test]
    public function can_create_from_string(string $value, string $expectedOutput): void
    {
        $polygon = Polygon::fromString($value);
        $this->assertSame($expectedOutput, (string) $polygon);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideValidPolygonStrings(): iterable
    {
        yield 'triangle' => ['((0,0),(1,0),(0,1))', '((0,0),(1,0),(0,1))'];
        yield 'square' => ['((0,0),(1,0),(1,1),(0,1))', '((0,0),(1,0),(1,1),(0,1))'];
        yield 'pentagon' => ['((0,0),(1,0),(1,1),(0,1),(0,0))', '((0,0),(1,0),(1,1),(0,1),(0,0))'];
        yield 'polygon with floats' => ['((1.5,2.5),(3.5,4.5),(5.5,6.5))', '((1.5,2.5),(3.5,4.5),(5.5,6.5))'];
        yield 'polygon with negative coordinates' => ['((-1,-2),(-3,-4),(-5,-6))', '((-1,-2),(-3,-4),(-5,-6))'];
        yield 'polygon with spaces' => ['((0, 0), (1, 0), (0, 1))', '((0,0),(1,0),(0,1))'];
    }

    #[DataProvider('provideInvalidPolygonStrings')]
    #[Test]
    public function throws_exception_for_invalid_format(string $value): void
    {
        $this->expectException(InvalidPolygonException::class);
        Polygon::fromString($value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideInvalidPolygonStrings(): iterable
    {
        yield 'empty string' => [''];
        yield 'plain text' => ['not a polygon'];
        yield 'single point' => ['((1,2))'];
        yield 'open path format' => ['[(1,2),(3,4),(5,6)]'];
        yield 'circle format' => ['<(1,2),3>'];
        yield 'line format' => ['{1,2,3}'];
    }

    #[Test]
    public function can_return_vertices_as_point_objects(): void
    {
        $polygon = Polygon::fromString('((0,0),(1,0),(0,1))');
        $vertices = $polygon->getVertices();
        $this->assertCount(3, $vertices);
        $this->assertSame(0.0, $vertices[0]->getX());
        $this->assertSame(0.0, $vertices[0]->getY());
        $this->assertSame(0.0, $vertices[2]->getX());
        $this->assertSame(1.0, $vertices[2]->getY());
    }

    #[Test]
    public function can_construct_from_point_objects(): void
    {
        $polygon = new Polygon(new Point(0.0, 0.0), new Point(1.0, 0.0), new Point(0.0, 1.0));
        $this->assertSame('((0,0),(1,0),(0,1))', (string) $polygon);
    }

    #[Test]
    public function throws_exception_for_too_few_vertices(): void
    {
        $this->expectException(InvalidPolygonException::class);
        new Polygon(new Point(1.0, 2.0));
    }
}
