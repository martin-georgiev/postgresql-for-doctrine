<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Interval;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class IntervalTest extends TestCase
{
    /**
     * @return array<string, array{value: string}>
     */
    public static function provideValidIntervalStrings(): array
    {
        return [
            'ISO 8601 years only' => ['value' => 'P1Y'],
            'ISO 8601 full' => ['value' => 'P1Y2M3DT4H5M6S'],
            'verbose single year' => ['value' => '1 year'],
            'PostgreSQL style' => ['value' => '1-2'],
            'PostgreSQL style with days and time' => ['value' => '1-2 3 4:05:06'],
        ];
    }

    #[DataProvider('provideValidIntervalStrings')]
    #[Test]
    public function can_create_from_valid_string(string $value): void
    {
        $interval = Interval::fromString($value);

        $this->assertSame($value, $interval->getValue());
        $this->assertSame($value, (string) $interval);
    }

    #[Test]
    public function throws_exception_for_empty_string(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Interval::fromString('');
    }

    #[Test]
    public function get_value_returns_stored_string(): void
    {
        $interval = new Interval('P1Y2M3DT4H5M6S');

        $this->assertSame('P1Y2M3DT4H5M6S', $interval->getValue());
    }

    #[Test]
    public function to_string_returns_stored_string(): void
    {
        $interval = new Interval('1 year 2 months');

        $this->assertSame('1 year 2 months', (string) $interval);
    }
}
