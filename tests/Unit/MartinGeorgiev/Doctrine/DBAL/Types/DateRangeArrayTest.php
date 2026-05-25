<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\DateRangeArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateRangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateRangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateRange as DateRangeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DateRangeArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private DateRangeArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new DateRangeArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('daterange[]', $this->fixture->getName());
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
        $this->assertEquals($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{
     *     phpValue: array<DateRangeValueObject|null>|null,
     *     postgresValue: string|null
     * }>
     */
    public static function provideValidTransformations(): array
    {
        $lower = new \DateTimeImmutable('2023-01-01');
        $upper = new \DateTimeImmutable('2023-12-31');

        return [
            'null' => [
                'phpValue' => null,
                'postgresValue' => null,
            ],
            'empty array' => [
                'phpValue' => [],
                'postgresValue' => '{}',
            ],
            'single range' => [
                'phpValue' => [new DateRangeValueObject($lower, $upper)],
                'postgresValue' => '{"[2023-01-01,2023-12-31)"}',
            ],
            'multiple ranges' => [
                'phpValue' => [
                    new DateRangeValueObject($lower, $upper),
                    new DateRangeValueObject($upper, null),
                ],
                'postgresValue' => '{"[2023-01-01,2023-12-31)","[2023-12-31,)"}',
            ],
            'inclusive upper bound' => [
                'phpValue' => [new DateRangeValueObject($lower, $upper, true, true)],
                'postgresValue' => '{"[2023-01-01,2023-12-31]"}',
            ],
            'array with null item' => [
                'phpValue' => [new DateRangeValueObject($lower, $upper), null],
                'postgresValue' => '{"[2023-01-01,2023-12-31)",NULL}',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidDateRangeArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'string instead of array' => ['not-an-array'],
            'integer instead of array' => [42],
            'boolean instead of array' => [false],
        ];
    }

    #[DataProvider('provideInvalidArrayItemInputs')]
    #[Test]
    public function throws_exception_for_invalid_array_item_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidDateRangeArrayItemForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidArrayItemInputs(): array
    {
        return [
            'array containing strings' => [['[2023-01-01,2023-12-31)']],
            'array containing integers' => [[42]],
            'array containing objects' => [[new \stdClass()]],
        ];
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
            'null value' => [null],
            'valid range object' => [new DateRangeValueObject(
                new \DateTimeImmutable('2023-01-01'),
                new \DateTimeImmutable('2023-12-31')
            )],
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
            'string' => ['[2023-01-01,2023-12-31)'],
            'integer' => [42],
            'boolean' => [true],
            'object' => [new \stdClass()],
        ];
    }

    #[Test]
    public function can_transform_null_item_for_php(): void
    {
        $this->assertNull($this->fixture->transformArrayItemForPHP(null));
    }

    #[Test]
    public function throws_exception_for_non_string_item_from_database(): void
    {
        $this->expectException(InvalidDateRangeArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP(42);
    }
}
