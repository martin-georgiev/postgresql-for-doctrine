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
        $this->assertFalse($numericMultirange->isEmpty());
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
            'inclusive upper bound' => ['{[1.5,5.5]}', '{[1.5,5.5]}'],
            'exclusive lower bound' => ['{(1.5,5.5)}', '{(1.5,5.5)}'],
        ];
    }

    #[DataProvider('provideInvalidFromStringCases')]
    #[Test]
    public function throws_on_invalid_format(string $input): void
    {
        $this->expectException(\InvalidArgumentException::class);

        NumericMultirange::fromString($input);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidFromStringCases(): array
    {
        return [
            'empty string' => [''],
            'missing braces' => ['[1.5,5.5),[10.5,20.5)'],
            'only opening brace' => ['{[1.5,5.5)'],
            'empty segment between commas' => ['{,}'],
            'empty leading segment' => ['{,[1.5,5.5)}'],
        ];
    }

    #[Test]
    public function get_ranges_returns_all_ranges(): void
    {
        $r1 = new NumericRange(1.5, 5.5);
        $r2 = new NumericRange(10.5, 20.5);
        $numericMultirange = new NumericMultirange([$r1, $r2]);

        $this->assertCount(2, $numericMultirange->getRanges());
        $this->assertSame($r1, $numericMultirange->getRanges()[0]);
        $this->assertSame($r2, $numericMultirange->getRanges()[1]);
    }
}
