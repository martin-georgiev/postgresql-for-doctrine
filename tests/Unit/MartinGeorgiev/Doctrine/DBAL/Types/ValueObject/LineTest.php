<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidLineException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Line;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LineTest extends TestCase
{
    #[DataProvider('provideValidLineStrings')]
    #[Test]
    public function can_create_from_string(string $value): void
    {
        $line = Line::fromString($value);
        $this->assertSame($value, (string) $line);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideValidLineStrings(): iterable
    {
        yield 'basic line' => ['{1,2,3}'];
        yield 'line with floats' => ['{1.5,2.5,3.5}'];
        yield 'line with negative coefficients' => ['{-1,-2,-3}'];
        yield 'line through origin' => ['{1,2,0}'];
        yield 'line with zero A' => ['{0,1,-2}'];
    }

    #[DataProvider('provideInvalidLineStrings')]
    #[Test]
    public function throws_exception_for_invalid_format(string $value): void
    {
        $this->expectException(InvalidLineException::class);
        Line::fromString($value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideInvalidLineStrings(): iterable
    {
        yield 'empty string' => [''];
        yield 'plain text' => ['not a line'];
        yield 'missing braces' => ['1,2,3'];
        yield 'too few coefficients' => ['{1,2}'];
        yield 'too many coefficients' => ['{1,2,3,4}'];
        yield 'box format' => ['(1,2),(3,4)'];
        yield 'circle format' => ['<(1,2),3>'];
    }

    #[Test]
    public function preserves_string_representation(): void
    {
        $value = '{1,2,3}';
        $line = new Line($value);
        $this->assertSame($value, (string) $line);
    }
}
