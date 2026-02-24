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
        $int8Multirange = new Int8Multirange([new Int8Range(1, 10)]);
        $this->assertSame('{[1,10)}', (string) $int8Multirange);
    }

    #[Test]
    public function handles_large_bigint_values(): void
    {
        $int8Multirange = new Int8Multirange([new Int8Range(1000000000, 9999999999)]);
        $this->assertSame('{[1000000000,9999999999)}', (string) $int8Multirange);
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
            'large values' => ['{[1000000000,9999999999)}', '{[1000000000,9999999999)}'],
        ];
    }
}
