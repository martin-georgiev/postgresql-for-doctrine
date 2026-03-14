<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateMultirange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateRange;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DateMultirangeTest extends TestCase
{
    #[Test]
    public function empty_multirange_produces_empty_string(): void
    {
        $dateMultirange = new DateMultirange([]);
        $this->assertSame('{}', (string) $dateMultirange);
        $this->assertTrue($dateMultirange->isEmpty());
    }

    #[Test]
    public function single_range_produces_correct_string(): void
    {
        $dateMultirange = new DateMultirange([
            new DateRange(new \DateTimeImmutable('2024-01-01'), new \DateTimeImmutable('2024-06-30')),
        ]);
        $this->assertSame('{[2024-01-01,2024-06-30)}', (string) $dateMultirange);
        $this->assertFalse($dateMultirange->isEmpty());
    }

    #[Test]
    public function multiple_ranges_produce_correct_string(): void
    {
        $dateMultirange = new DateMultirange([
            new DateRange(new \DateTimeImmutable('2024-01-01'), new \DateTimeImmutable('2024-03-31')),
            new DateRange(new \DateTimeImmutable('2024-07-01'), new \DateTimeImmutable('2024-12-31')),
        ]);
        $this->assertSame('{[2024-01-01,2024-03-31),[2024-07-01,2024-12-31)}', (string) $dateMultirange);
    }

    #[DataProvider('provideValidFromStringCases')]
    #[Test]
    public function can_parse_from_string(string $input, string $expectedString): void
    {
        $dateMultirange = DateMultirange::fromString($input);
        $this->assertSame($expectedString, (string) $dateMultirange);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideValidFromStringCases(): array
    {
        return [
            'empty multirange' => ['{}', '{}'],
            'single range' => ['{[2024-01-01,2024-06-30)}', '{[2024-01-01,2024-06-30)}'],
            'two ranges' => ['{[2024-01-01,2024-03-31),[2024-07-01,2024-12-31)}', '{[2024-01-01,2024-03-31),[2024-07-01,2024-12-31)}'],
        ];
    }

    #[DataProvider('provideInvalidFromStringCases')]
    #[Test]
    public function throws_on_invalid_format(string $input): void
    {
        $this->expectException(\InvalidArgumentException::class);

        DateMultirange::fromString($input);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidFromStringCases(): array
    {
        return [
            'empty string' => [''],
            'missing braces' => ['[2024-01-01,2024-06-30)'],
            'only opening brace' => ['{[2024-01-01,2024-06-30)'],
            'empty segment between commas' => ['{,}'],
        ];
    }

    #[Test]
    public function get_ranges_returns_all_ranges(): void
    {
        $r1 = new DateRange(new \DateTimeImmutable('2024-01-01'), new \DateTimeImmutable('2024-03-31'));
        $r2 = new DateRange(new \DateTimeImmutable('2024-07-01'), new \DateTimeImmutable('2024-12-31'));
        $dateMultirange = new DateMultirange([$r1, $r2]);

        $this->assertCount(2, $dateMultirange->getRanges());
        $this->assertSame($r1, $dateMultirange->getRanges()[0]);
        $this->assertSame($r2, $dateMultirange->getRanges()[1]);
    }
}
