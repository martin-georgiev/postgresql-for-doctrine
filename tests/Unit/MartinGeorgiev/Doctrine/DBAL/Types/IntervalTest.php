<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidIntervalForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidIntervalForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Interval;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Interval as IntervalValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class IntervalTest extends TestCase
{
    private MockObject&PostgreSQLPlatform $platform;

    private Interval $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(PostgreSQLPlatform::class);
        $this->fixture = new Interval();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('interval', $this->fixture->getName());
    }

    #[Test]
    public function converts_null_to_database_value(): void
    {
        $this->assertNull($this->fixture->convertToDatabaseValue(null, $this->platform));
    }

    #[Test]
    public function converts_null_to_php_value(): void
    {
        $this->assertNull($this->fixture->convertToPHPValue(null, $this->platform));
    }

    #[Test]
    public function converts_empty_string_to_null(): void
    {
        $this->assertNull($this->fixture->convertToPHPValue('', $this->platform));
    }

    #[DataProvider('provideValidPostgresOutputStrings')]
    #[Test]
    public function can_transform_to_php_value(string $postgresOutput): void
    {
        $result = $this->fixture->convertToPHPValue($postgresOutput, $this->platform);

        $this->assertInstanceOf(IntervalValueObject::class, $result);
        $this->assertSame($postgresOutput, (string) $result);
    }

    #[DataProvider('provideValidPostgresOutputStrings')]
    #[Test]
    public function can_transform_from_php_value_with_value_object(string $postgresOutput): void
    {
        $interval = IntervalValueObject::fromString($postgresOutput);
        $result = $this->fixture->convertToDatabaseValue($interval, $this->platform);

        $this->assertSame($postgresOutput, $result);
    }

    #[DataProvider('provideValidPostgresOutputStrings')]
    #[Test]
    public function can_transform_from_php_value_with_string(string $postgresOutput): void
    {
        $result = $this->fixture->convertToDatabaseValue($postgresOutput, $this->platform);

        $this->assertSame($postgresOutput, $result);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideValidPostgresOutputStrings(): array
    {
        return [
            'year' => ['1 year'],
            'years' => ['2 years'],
            'mons' => ['2 mons'],
            'days' => ['3 days'],
            'time only' => ['04:05:06'],
            'full' => ['1 year 2 mons 3 days 04:05:06'],
            'zero' => ['00:00:00'],
            'negative time' => ['-04:05:06'],
            'negative year' => ['-1 year'],
        ];
    }

    #[Test]
    public function can_transform_to_php_value_with_value_object(): void
    {
        $interval = IntervalValueObject::fromString('1 year 2 mons 3 days 04:05:06');
        $result = $this->fixture->convertToPHPValue($interval, $this->platform);

        $this->assertSame($interval, $result);
    }

    #[Test]
    public function can_transform_from_php_value_with_date_interval(): void
    {
        $dateInterval = new \DateInterval('P1Y2M3DT4H5M6S');
        $result = $this->fixture->convertToDatabaseValue($dateInterval, $this->platform);

        $this->assertSame('1 year 2 mons 3 days 04:05:06', $result);
    }

    #[DataProvider('provideNormalizedInputStrings')]
    #[Test]
    public function normalizes_various_input_formats(string $input, string $expectedOutput): void
    {
        $result = $this->fixture->convertToDatabaseValue($input, $this->platform);

        $this->assertSame($expectedOutput, $result);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideNormalizedInputStrings(): array
    {
        return [
            'ISO 8601 full' => ['P1Y2M3DT4H5M6S', '1 year 2 mons 3 days 04:05:06'],
            'ISO 8601 years only' => ['P1Y', '1 year'],
            'ISO 8601 time only' => ['PT4H5M6S', '04:05:06'],
            'verbose months' => ['2 months', '2 mons'],
            'verbose full' => ['1 year 2 months 3 days 4 hours 5 minutes 6 seconds', '1 year 2 mons 3 days 04:05:06'],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidIntervalForPHPException::class);
        $this->fixture->convertToPHPValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'integer input' => [42],
            'float input' => [1.5],
            'boolean input' => [true],
            'array input' => [['1 year']],
            'object input' => [new \stdClass()],
            'invalid string' => ['not-an-interval'],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidIntervalForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'integer input' => [42],
            'float input' => [1.5],
            'boolean input' => [true],
            'array input' => [['1 year']],
            'object input' => [new \stdClass()],
            'empty string' => [''],
            'invalid string' => ['not-an-interval'],
        ];
    }
}
