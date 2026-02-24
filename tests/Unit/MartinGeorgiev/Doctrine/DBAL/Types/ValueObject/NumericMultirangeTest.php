<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericMultirange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class NumericMultirangeTest extends TestCase
{
    #[Test]
    public function empty_multirange_produces_empty_string(): void
    {
        $numericMultirange = new NumericMultirange([]);
        $this->assertSame('{}', (string) $numericMultirange);
        $this->assertTrue($numericMultirange->isEmpty());
    }

    #[Test]
    public function single_range_produces_correct_string(): void
    {
        $numericMultirange = new NumericMultirange([new NumericRange(1.5, 10.5)]);
        $this->assertSame('{[1.5,10.5)}', (string) $numericMultirange);
    }

    #[Test]
    public function multiple_ranges_produce_correct_string(): void
    {
        $numericMultirange = new NumericMultirange([
            new NumericRange(1, 5),
            new NumericRange(10, 20),
        ]);
        $this->assertSame('{[1,5),[10,20)}', (string) $numericMultirange);
    }

    #[DataProvider('provideValidFromStringCases')]
    #[Test]
    public function can_parse_from_string(string $input, string $expectedString): void
    {
        $numericMultirange = NumericMultirange::fromString($input);
        $this->assertSame($expectedString, (string) $numericMultirange);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideValidFromStringCases(): array
    {
        return [
            'empty multirange' => ['{}', '{}'],
            'single integer range' => ['{[1,10)}', '{[1,10)}'],
            'two decimal ranges' => ['{[1.5,5.5),[10.5,20.5)}', '{[1.5,5.5),[10.5,20.5)}'],
            'mixed integer and decimal' => ['{[1,5),[10.5,20.5)}', '{[1,5),[10.5,20.5)}'],
        ];
    }
}
