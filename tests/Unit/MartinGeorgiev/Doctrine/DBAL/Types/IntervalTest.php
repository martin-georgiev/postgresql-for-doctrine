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

final class IntervalTest extends TestCase
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

    /**
     * @return array<string, array{intervalString: string}>
     */
    public static function provideValidIntervalStrings(): array
    {
        return [
            'ISO 8601 full' => ['intervalString' => 'P1Y2M3DT4H5M6S'],
            'ISO 8601 years only' => ['intervalString' => 'P1Y'],
            'ISO 8601 time only' => ['intervalString' => 'PT4H5M6S'],
            'verbose single year' => ['intervalString' => '1 year'],
            'verbose combined' => ['intervalString' => '1 year 2 months 3 days'],
            'verbose with time' => ['intervalString' => '1 year 2 months 3 days 4 hours 5 minutes 6 seconds'],
            'PostgreSQL style' => ['intervalString' => '1-2'],
            'PostgreSQL style with days and time' => ['intervalString' => '1-2 3 4:05:06'],
            'time only' => ['intervalString' => '04:05:06'],
        ];
    }

    #[DataProvider('provideValidIntervalStrings')]
    #[Test]
    public function converts_valid_interval_string_to_php_value(string $intervalString): void
    {
        $result = $this->fixture->convertToPHPValue($intervalString, $this->platform);

        $this->assertInstanceOf(IntervalValueObject::class, $result);
        $this->assertSame($intervalString, $result->getValue());
    }

    #[DataProvider('provideValidIntervalStrings')]
    #[Test]
    public function converts_interval_value_object_to_database_value(string $intervalString): void
    {
        $interval = IntervalValueObject::fromString($intervalString);
        $result = $this->fixture->convertToDatabaseValue($interval, $this->platform);

        $this->assertSame($intervalString, $result);
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
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'string input' => ['P1Y'],
            'integer input' => [42],
            'float input' => [1.5],
            'boolean input' => [true],
            'array input' => [['1 year']],
            'object input' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidIntervalForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($value, $this->platform);
    }
}
