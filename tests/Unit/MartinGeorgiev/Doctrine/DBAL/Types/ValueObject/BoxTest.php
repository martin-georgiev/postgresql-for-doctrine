<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Box;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidBoxException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BoxTest extends TestCase
{
    #[DataProvider('provideValidBoxStrings')]
    #[Test]
    public function can_create_from_string(string $value, string $expectedOutput): void
    {
        $box = Box::fromString($value);
        $this->assertSame($expectedOutput, (string) $box);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideValidBoxStrings(): iterable
    {
        yield 'basic integer coordinates' => ['(1,2),(3,4)', '(1,2),(3,4)'];
        yield 'float coordinates' => ['(1.5,2.5),(3.5,4.5)', '(1.5,2.5),(3.5,4.5)'];
        yield 'negative coordinates' => ['(-1,-2),(-3,-4)', '(-1,-2),(-3,-4)'];
        yield 'zero coordinates' => ['(0,0),(1,1)', '(0,0),(1,1)'];
        yield 'mixed positive and negative' => ['(-1,2),(3,-4)', '(-1,2),(3,-4)'];
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
    public function can_return_point_values_via_getters(): void
    {
        $box = Box::fromString('(1.5,2.5),(3.5,4.5)');
        $this->assertSame(1.5, $box->getUpperRight()->getX());
        $this->assertSame(2.5, $box->getUpperRight()->getY());
        $this->assertSame(3.5, $box->getLowerLeft()->getX());
        $this->assertSame(4.5, $box->getLowerLeft()->getY());
    }

    #[Test]
    public function can_construct_from_points(): void
    {
        $box = new Box(new Point(1.0, 2.0), new Point(3.0, 4.0));
        $this->assertSame('(1,2),(3,4)', (string) $box);
    }
}
