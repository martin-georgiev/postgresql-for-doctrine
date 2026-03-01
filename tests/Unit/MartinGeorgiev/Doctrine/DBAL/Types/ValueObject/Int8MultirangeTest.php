<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Multirange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Range;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class Int8MultirangeTest extends TestCase
{
    #[Test]
    public function empty_multirange_produces_empty_string(): void
    {
        $int8Multirange = new Int8Multirange([]);
        $this->assertSame('{}', (string) $int8Multirange);
        $this->assertTrue($int8Multirange->isEmpty());
    }

    #[Test]
    public function single_range_produces_correct_string(): void
    {
        $int8Multirange = new Int8Multirange([new Int8Range(1000000000, 9999999999)]);
        $this->assertSame('{[1000000000,9999999999)}', (string) $int8Multirange);
        $this->assertFalse($int8Multirange->isEmpty());
    }

    #[Test]
    public function multiple_ranges_produce_correct_string(): void
    {
        $int8Multirange = new Int8Multirange([
            new Int8Range(1, 5),
            new Int8Range(10, 20),
        ]);
        $this->assertSame('{[1,5),[10,20)}', (string) $int8Multirange);
    }

    #[Test]
    public function handles_negative_values(): void
    {
        $int8Multirange = new Int8Multirange([new Int8Range(-9999999999, -1)]);
        $this->assertSame('{[-9999999999,-1)}', (string) $int8Multirange);
    }

    #[DataProvider('provideValidFromStringCases')]
    #[Test]
    public function can_parse_from_string(string $input, string $expectedString): void
    {
        $int8Multirange = Int8Multirange::fromString($input);
        $this->assertSame($expectedString, (string) $int8Multirange);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideValidFromStringCases(): array
    {
        return [
            'empty multirange' => ['{}', '{}'],
            'single range' => ['{[1,10)}', '{[1,10)}'],
            'two ranges' => ['{[1,5),[10,20)}', '{[1,5),[10,20)}'],
            'inclusive upper bound' => ['{[1,5]}', '{[1,5]}'],
            'exclusive lower bound' => ['{(1,5)}', '{(1,5)}'],
            'large values' => ['{[1000000000,9999999999)}', '{[1000000000,9999999999)}'],
        ];
    }

    #[DataProvider('provideInvalidFromStringCases')]
    #[Test]
    public function throws_on_invalid_format(string $input): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Int8Multirange::fromString($input);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidFromStringCases(): array
    {
        return [
            'empty string' => [''],
            'missing braces' => ['[1,5),[10,20)'],
            'only opening brace' => ['{[1,5)'],
            'empty segment between commas' => ['{,}'],
            'empty leading segment' => ['{,[1,5)}'],
            'unbalanced brackets' => ['{[1,5}'],
            'trailing empty segment' => ['{[1,5),}'],
        ];
    }

    #[Test]
    public function get_ranges_returns_all_ranges(): void
    {
        $r1 = new Int8Range(1, 5);
        $r2 = new Int8Range(10, 20);
        $int8Multirange = new Int8Multirange([$r1, $r2]);

        $this->assertCount(2, $int8Multirange->getRanges());
        $this->assertSame($r1, $int8Multirange->getRanges()[0]);
        $this->assertSame($r2, $int8Multirange->getRanges()[1]);
    }
}
