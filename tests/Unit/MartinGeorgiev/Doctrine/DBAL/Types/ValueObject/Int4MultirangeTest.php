<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Multirange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Range;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class Int4MultirangeTest extends TestCase
{
    #[Test]
    public function empty_multirange_produces_empty_string(): void
    {
        $int4Multirange = new Int4Multirange([]);
        $this->assertSame('{}', (string) $int4Multirange);
        $this->assertTrue($int4Multirange->isEmpty());
    }

    #[Test]
    public function single_range_produces_correct_string(): void
    {
        $int4Multirange = new Int4Multirange([new Int4Range(1, 10)]);
        $this->assertSame('{[1,10)}', (string) $int4Multirange);
        $this->assertFalse($int4Multirange->isEmpty());
    }

    #[Test]
    public function multiple_ranges_produce_correct_string(): void
    {
        $int4Multirange = new Int4Multirange([
            new Int4Range(1, 5),
            new Int4Range(10, 20),
        ]);
        $this->assertSame('{[1,5),[10,20)}', (string) $int4Multirange);
    }

    #[DataProvider('provideValidFromStringCases')]
    #[Test]
    public function can_parse_from_string(string $input, string $expectedString): void
    {
        $int4Multirange = Int4Multirange::fromString($input);
        $this->assertSame($expectedString, (string) $int4Multirange);
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
        ];
    }

    #[DataProvider('provideInvalidFromStringCases')]
    #[Test]
    public function throws_on_invalid_format(string $input): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Int4Multirange::fromString($input);
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
        ];
    }

    #[Test]
    public function get_ranges_returns_all_ranges(): void
    {
        $r1 = new Int4Range(1, 5);
        $r2 = new Int4Range(10, 20);
        $int4Multirange = new Int4Multirange([$r1, $r2]);

        $this->assertCount(2, $int4Multirange->getRanges());
        $this->assertSame($r1, $int4Multirange->getRanges()[0]);
        $this->assertSame($r2, $int4Multirange->getRanges()[1]);
    }
}
