<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Circle;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidCircleException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CircleTest extends TestCase
{
    #[DataProvider('provideValidCircleStrings')]
    #[Test]
    public function can_create_from_string(string $value): void
    {
        $circle = Circle::fromString($value);
        $this->assertSame($value, (string) $circle);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideValidCircleStrings(): iterable
    {
        yield 'basic circle' => ['<(1,2),3>'];
        yield 'circle with floats' => ['<(1.5,2.5),3.5>'];
        yield 'circle with negative center' => ['<(-1,-2),5>'];
        yield 'circle at origin' => ['<(0,0),1>'];
        yield 'circle with float radius' => ['<(0,0),1.5>'];
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
    }

    #[Test]
    public function preserves_string_representation(): void
    {
        $value = '<(1,2),3>';
        $circle = new Circle($value);
        $this->assertSame($value, (string) $circle);
    }
}
