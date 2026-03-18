<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidIntervalArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidIntervalArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\IntervalArray;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Interval as IntervalValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class IntervalArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private IntervalArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new IntervalArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('interval[]', $this->fixture->getName());
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
    public function converts_empty_array_to_database_value(): void
    {
        $this->assertSame('{}', $this->fixture->convertToDatabaseValue([], $this->platform));
    }

    #[Test]
    public function converts_empty_postgres_array_to_php_value(): void
    {
        $this->assertSame([], $this->fixture->convertToPHPValue('{}', $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $actual = $this->fixture->convertToPHPValue($postgresValue, $this->platform);

        if ($phpValue === null) {
            $this->assertNull($actual);

            return;
        }

        $this->assertIsArray($actual);
        $this->assertCount(\count($phpValue), $actual);

        foreach ($phpValue as $index => $expectedItem) {
            $this->assertInstanceOf(IntervalValueObject::class, $actual[$index]);
            $this->assertSame((string) $expectedItem, (string) $actual[$index]);
        }
    }

    /**
     * @return array<string, array{
     *     phpValue: array<IntervalValueObject>|null,
     *     postgresValue: string|null
     * }>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'null' => [
                'phpValue' => null,
                'postgresValue' => null,
            ],
            'empty array' => [
                'phpValue' => [],
                'postgresValue' => '{}',
            ],
            'single year interval' => [
                'phpValue' => [IntervalValueObject::fromString('1 year')],
                'postgresValue' => '{"1 year"}',
            ],
            'single time interval' => [
                'phpValue' => [IntervalValueObject::fromString('04:05:06')],
                'postgresValue' => '{"04:05:06"}',
            ],
            'full interval' => [
                'phpValue' => [IntervalValueObject::fromString('1 year 2 mons 3 days 04:05:06')],
                'postgresValue' => '{"1 year 2 mons 3 days 04:05:06"}',
            ],
            'multiple intervals' => [
                'phpValue' => [
                    IntervalValueObject::fromString('1 year'),
                    IntervalValueObject::fromString('04:05:06'),
                ],
                'postgresValue' => '{"1 year","04:05:06"}',
            ],
            'zero interval' => [
                'phpValue' => [IntervalValueObject::fromString('00:00:00')],
                'postgresValue' => '{"00:00:00"}',
            ],
        ];
    }

    #[Test]
    public function accepts_string_item_for_database(): void
    {
        $result = $this->fixture->convertToDatabaseValue(['1 year'], $this->platform);

        $this->assertSame('{"1 year"}', $result);
    }

    #[Test]
    public function accepts_date_interval_item_for_database(): void
    {
        $dateInterval = new \DateInterval('P1Y2M3DT4H5M6S');
        $result = $this->fixture->convertToDatabaseValue([$dateInterval], $this->platform);

        $this->assertSame('{"1 year 2 mons 3 days 04:05:06"}', $result);
    }

    #[DataProvider('provideValidArrayItemsForDatabase')]
    #[Test]
    public function can_validate_valid_array_item_for_database(mixed $value): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase($value));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideValidArrayItemsForDatabase(): array
    {
        return [
            'null' => [null],
            'IntervalValueObject year' => [IntervalValueObject::fromString('1 year')],
            'IntervalValueObject time' => [IntervalValueObject::fromString('04:05:06')],
            'IntervalValueObject full' => [IntervalValueObject::fromString('1 year 2 mons 3 days 04:05:06')],
            'DateInterval' => [new \DateInterval('P1Y')],
            'string year' => ['1 year'],
            'string time' => ['04:05:06'],
            'string ISO 8601' => ['P1Y2M3DT4H5M6S'],
        ];
    }

    #[DataProvider('provideInvalidArrayItemsForDatabase')]
    #[Test]
    public function can_validate_invalid_array_item_for_database(mixed $value): void
    {
        $this->assertFalse($this->fixture->isValidArrayItemForDatabase($value));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidArrayItemsForDatabase(): array
    {
        return [
            'integer' => [42],
            'float' => [1.5],
            'boolean' => [true],
            'empty string' => [''],
            'invalid string' => ['not-an-interval'],
            'object' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidIntervalArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidTypeInputs(): array
    {
        return [
            'integer' => [42],
            'float' => [3.14],
            'boolean' => [true],
            'string instead of array' => ['1 year'],
        ];
    }

    #[Test]
    public function throws_exception_for_invalid_string_item_in_database_array(): void
    {
        $this->expectException(InvalidIntervalArrayItemForDatabaseException::class);
        $this->fixture->convertToDatabaseValue(['not-an-interval'], $this->platform);
    }

    #[Test]
    public function throws_exception_for_invalid_type_item_in_database_array(): void
    {
        $this->expectException(InvalidIntervalArrayItemForDatabaseException::class);
        $this->fixture->convertToDatabaseValue([42], $this->platform);
    }

    #[Test]
    public function transform_array_item_for_php_returns_null_for_null(): void
    {
        $this->assertNull($this->fixture->transformArrayItemForPHP(null));
    }

    #[Test]
    public function transform_array_item_for_php_returns_interval_value_object_for_valid_string(): void
    {
        $result = $this->fixture->transformArrayItemForPHP('1 year');

        $this->assertInstanceOf(IntervalValueObject::class, $result);
        $this->assertSame('1 year', (string) $result);
    }

    #[Test]
    public function transform_array_item_for_php_throws_for_non_string(): void
    {
        $this->expectException(InvalidIntervalArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP(42);
    }

    #[Test]
    public function transform_array_item_for_php_throws_for_invalid_format(): void
    {
        $this->expectException(InvalidIntervalArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP('not-an-interval');
    }
}
