<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Box;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidBoxException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BoxTest extends TestCase
{
    #[DataProvider('provideValidBoxStrings')]
    #[Test]
    public function can_create_from_string(string $value): void
    {
        $box = Box::fromString($value);
        $this->assertSame($value, (string) $box);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideValidBoxStrings(): iterable
    {
        yield 'basic integer coordinates' => ['(1,2),(3,4)'];
        yield 'float coordinates' => ['(1.5,2.5),(3.5,4.5)'];
        yield 'negative coordinates' => ['(-1,-2),(-3,-4)'];
        yield 'zero coordinates' => ['(0,0),(1,1)'];
        yield 'mixed positive and negative' => ['(-1,2),(3,-4)'];
    }

    #[DataProvider('provideInvalidBoxStrings')]
    #[Test]
    public function throws_exception_for_invalid_format(string $value): void
    {
        $this->expectException(InvalidBoxException::class);
        Box::fromString($value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideInvalidBoxStrings(): iterable
    {
        yield 'empty string' => [''];
        yield 'plain text' => ['not a box'];
        yield 'single point' => ['(1,2)'];
        yield 'missing comma separator between points' => ['(1,2)(3,4)'];
        yield 'circle format' => ['<(1,2),3>'];
        yield 'line format' => ['{1,2,3}'];
    }

    #[Test]
    public function preserves_string_representation(): void
    {
        $value = '(1,2),(3,4)';
        $box = new Box($value);
        $this->assertSame($value, (string) $box);
    }
}
