<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsMultirange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsRange;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TsMultirangeTest extends TestCase
{
    #[Test]
    public function empty_multirange_produces_empty_string(): void
    {
        $tsMultirange = new TsMultirange([]);
        $this->assertSame('{}', (string) $tsMultirange);
        $this->assertTrue($tsMultirange->isEmpty());
    }

    #[Test]
    public function single_range_produces_correct_string(): void
    {
        $tsMultirange = new TsMultirange([
            new TsRange(new \DateTimeImmutable('2024-01-01 09:00:00'), new \DateTimeImmutable('2024-01-01 17:00:00')),
        ]);
        $this->assertSame('{[2024-01-01 09:00:00.000000,2024-01-01 17:00:00.000000)}', (string) $tsMultirange);
        $this->assertFalse($tsMultirange->isEmpty());
    }

    #[Test]
    public function multiple_ranges_produce_correct_string(): void
    {
        $tsMultirange = new TsMultirange([
            new TsRange(new \DateTimeImmutable('2024-01-01 09:00:00'), new \DateTimeImmutable('2024-01-01 12:00:00')),
            new TsRange(new \DateTimeImmutable('2024-01-01 14:00:00'), new \DateTimeImmutable('2024-01-01 17:00:00')),
        ]);
        $this->assertSame('{[2024-01-01 09:00:00.000000,2024-01-01 12:00:00.000000),[2024-01-01 14:00:00.000000,2024-01-01 17:00:00.000000)}', (string) $tsMultirange);
    }

    #[DataProvider('provideValidFromStringCases')]
    #[Test]
    public function can_parse_from_string(string $input, string $expectedString): void
    {
        $tsMultirange = TsMultirange::fromString($input);
        $this->assertSame($expectedString, (string) $tsMultirange);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideValidFromStringCases(): array
    {
        return [
            'empty multirange' => ['{}', '{}'],
            'single range' => [
                '{[2024-01-01 09:00:00,2024-01-01 17:00:00)}',
                '{[2024-01-01 09:00:00.000000,2024-01-01 17:00:00.000000)}',
            ],
            'two ranges' => [
                '{[2024-01-01 09:00:00,2024-01-01 12:00:00),[2024-01-01 14:00:00,2024-01-01 17:00:00)}',
                '{[2024-01-01 09:00:00.000000,2024-01-01 12:00:00.000000),[2024-01-01 14:00:00.000000,2024-01-01 17:00:00.000000)}',
            ],
        ];
    }

    #[DataProvider('provideInvalidFromStringCases')]
    #[Test]
    public function throws_on_invalid_format(string $input): void
    {
        $this->expectException(\InvalidArgumentException::class);

        TsMultirange::fromString($input);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidFromStringCases(): array
    {
        return [
            'empty string' => [''],
            'missing braces' => ['[2024-01-01 09:00:00,2024-01-01 17:00:00)'],
            'only opening brace' => ['{[2024-01-01 09:00:00,2024-01-01 17:00:00)'],
            'empty segment between commas' => ['{,}'],
        ];
    }

    #[Test]
    public function get_ranges_returns_all_ranges(): void
    {
        $r1 = new TsRange(new \DateTimeImmutable('2024-01-01 09:00:00'), new \DateTimeImmutable('2024-01-01 12:00:00'));
        $r2 = new TsRange(new \DateTimeImmutable('2024-01-01 14:00:00'), new \DateTimeImmutable('2024-01-01 17:00:00'));
        $tsMultirange = new TsMultirange([$r1, $r2]);

        $this->assertCount(2, $tsMultirange->getRanges());
        $this->assertSame($r1, $tsMultirange->getRanges()[0]);
        $this->assertSame($r2, $tsMultirange->getRanges()[1]);
    }
}
