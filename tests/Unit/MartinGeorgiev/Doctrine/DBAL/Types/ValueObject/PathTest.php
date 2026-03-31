<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidPathException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Path;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    #[DataProvider('provideValidPathStrings')]
    #[Test]
    public function can_create_from_string(string $value, string $expectedOutput): void
    {
        $path = Path::fromString($value);
        $this->assertSame($expectedOutput, (string) $path);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideValidPathStrings(): iterable
    {
        yield 'open path with two points' => ['[(1,2),(3,4)]', '[(1,2),(3,4)]'];
        yield 'closed path with two points' => ['((1,2),(3,4))', '((1,2),(3,4))'];
        yield 'open path with three points' => ['[(1,2),(3,4),(5,6)]', '[(1,2),(3,4),(5,6)]'];
        yield 'closed path with four points' => ['((0,0),(1,0),(1,1),(0,1))', '((0,0),(1,0),(1,1),(0,1))'];
        yield 'path with floats' => ['[(1.5,2.5),(3.5,4.5)]', '[(1.5,2.5),(3.5,4.5)]'];
        yield 'path with negative coordinates' => ['[(-1,-2),(-3,-4)]', '[(-1,-2),(-3,-4)]'];
        yield 'path with spaces' => ['[(1, 2), (3, 4)]', '[(1,2),(3,4)]'];
    }

    #[DataProvider('provideInvalidPathStrings')]
    #[Test]
    public function throws_exception_for_invalid_format(string $value): void
    {
        $this->expectException(InvalidPathException::class);
        Path::fromString($value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideInvalidPathStrings(): iterable
    {
        yield 'empty string' => [''];
        yield 'plain text' => ['not a path'];
        yield 'bare point list' => ['(1,2),(3,4)'];
        yield 'circle format' => ['<(1,2),3>'];
        yield 'line format' => ['{1,2,3}'];
        yield 'mismatched brackets' => ['[(1,2),(3,4))'];
    }

    #[Test]
    public function returns_points_as_point_objects(): void
    {
        $path = Path::fromString('[(1,2),(3,4),(5,6)]');
        $points = $path->getPoints();
        $this->assertCount(3, $points);
        $this->assertSame(1.0, $points[0]->getX());
        $this->assertSame(2.0, $points[0]->getY());
        $this->assertSame(5.0, $points[2]->getX());
        $this->assertSame(6.0, $points[2]->getY());
    }

    #[Test]
    public function returns_is_open_for_open_path(): void
    {
        $path = Path::fromString('[(1,2),(3,4)]');
        $this->assertTrue($path->isOpen());
    }

    #[Test]
    public function returns_is_open_for_closed_path(): void
    {
        $path = Path::fromString('((1,2),(3,4))');
        $this->assertFalse($path->isOpen());
    }

    #[Test]
    public function can_construct_from_point_objects(): void
    {
        $path = new Path(true, new Point(1.0, 2.0), new Point(3.0, 4.0));
        $this->assertSame('[(1,2),(3,4)]', (string) $path);
        $this->assertTrue($path->isOpen());
    }
}
