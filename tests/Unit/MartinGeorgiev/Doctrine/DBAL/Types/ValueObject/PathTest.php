<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidPathException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Path;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    #[DataProvider('provideValidPathStrings')]
    #[Test]
    public function can_create_from_string(string $value): void
    {
        $path = Path::fromString($value);
        $this->assertSame($value, (string) $path);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideValidPathStrings(): iterable
    {
        yield 'open path with two points' => ['[(1,2),(3,4)]'];
        yield 'closed path with two points' => ['((1,2),(3,4))'];
        yield 'open path with three points' => ['[(1,2),(3,4),(5,6)]'];
        yield 'closed path with four points' => ['((0,0),(1,0),(1,1),(0,1))'];
        yield 'path with floats' => ['[(1.5,2.5),(3.5,4.5)]'];
        yield 'path with negative coordinates' => ['[(-1,-2),(-3,-4)]'];
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
    public function preserves_string_representation(): void
    {
        $value = '[(1,2),(3,4)]';
        $path = new Path($value);
        $this->assertSame($value, (string) $path);
    }
}
