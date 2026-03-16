<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidPolygonException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Polygon;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PolygonTest extends TestCase
{
    #[DataProvider('provideValidPolygonStrings')]
    #[Test]
    public function can_create_from_string(string $value): void
    {
        $polygon = Polygon::fromString($value);
        $this->assertSame($value, (string) $polygon);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideValidPolygonStrings(): iterable
    {
        yield 'triangle' => ['((0,0),(1,0),(0,1))'];
        yield 'square' => ['((0,0),(1,0),(1,1),(0,1))'];
        yield 'pentagon' => ['((0,0),(1,0),(1,1),(0,1),(0,0))'];
        yield 'polygon with floats' => ['((1.5,2.5),(3.5,4.5),(5.5,6.5))'];
        yield 'polygon with negative coordinates' => ['((-1,-2),(-3,-4),(-5,-6))'];
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
    public function preserves_string_representation(): void
    {
        $value = '((0,0),(1,0),(0,1))';
        $polygon = new Polygon($value);
        $this->assertSame($value, (string) $polygon);
    }
}
