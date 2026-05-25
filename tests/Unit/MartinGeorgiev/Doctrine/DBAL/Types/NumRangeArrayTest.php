<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidNumRangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidNumRangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\NumRangeArray;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange as NumericRangeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NumRangeArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private NumRangeArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new NumRangeArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('numrange[]', $this->fixture->getName());
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
     *     phpValue: array<NumericRangeValueObject|null>|null,
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
            'single range' => [
                'phpValue' => [new NumericRangeValueObject(1, 10)],
                'postgresValue' => '{"[1,10)"}',
            ],
            'multiple ranges' => [
                'phpValue' => [
                    new NumericRangeValueObject(1, 10),
                    new NumericRangeValueObject(20, 30),
                ],
                'postgresValue' => '{"[1,10)","[20,30)"}',
            ],
            'float bounds' => [
                'phpValue' => [new NumericRangeValueObject(1.5, 9.9)],
                'postgresValue' => '{"[1.5,9.9)"}',
            ],
            'array with null item' => [
                'phpValue' => [new NumericRangeValueObject(1, 10), null],
                'postgresValue' => '{"[1,10)",NULL}',
            ],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidNumRangeArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidTypeInputs(): array
    {
        return [
            'string instead of array' => ['not-an-array'],
            'integer instead of array' => [42],
            'boolean instead of array' => [false],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidNumRangeArrayItemForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'array containing strings' => [['[1,10)']],
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
            'valid range object' => [new NumericRangeValueObject(1, 10)],
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
            'string' => ['[1,10)'],
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
        $this->expectException(InvalidNumRangeArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP(42);
    }

    #[DataProvider('provideInvalidFormatItemsFromDatabase')]
    #[Test]
    public function throws_exception_for_invalid_format_item_from_database(string $value): void
    {
        $this->expectException(InvalidNumRangeArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP($value);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidFormatItemsFromDatabase(): array
    {
        return [
            'plain string' => ['not-a-valid-range'],
            'missing brackets' => ['1,10'],
            'incomplete range' => ['[1.5'],
        ];
    }
}
