<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInt8RangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInt8RangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Int8RangeArray;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Range as Int8RangeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class Int8RangeArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Int8RangeArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Int8RangeArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('int8range[]', $this->fixture->getName());
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
     *     phpValue: array<Int8RangeValueObject|null>|null,
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
                'phpValue' => [new Int8RangeValueObject(1, 1000)],
                'postgresValue' => '{"[1,1000)"}',
            ],
            'multiple ranges' => [
                'phpValue' => [
                    new Int8RangeValueObject(1, 1000),
                    new Int8RangeValueObject(2000, 5000),
                ],
                'postgresValue' => '{"[1,1000)","[2000,5000)"}',
            ],
            'inclusive upper bound' => [
                'phpValue' => [new Int8RangeValueObject(1, 100, true, true)],
                'postgresValue' => '{"[1,100]"}',
            ],
            'array with null item' => [
                'phpValue' => [new Int8RangeValueObject(1, 100), null],
                'postgresValue' => '{"[1,100)",NULL}',
            ],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidInt8RangeArrayItemForPHPException::class);
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
        $this->expectException(InvalidInt8RangeArrayItemForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'array containing strings' => [['[1,1000)']],
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
            'valid range object' => [new Int8RangeValueObject(1, 1000)],
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
            'string' => ['[1,1000)'],
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
        $this->expectException(InvalidInt8RangeArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP(42);
    }

    #[DataProvider('provideInvalidFormatItemsFromDatabase')]
    #[Test]
    public function throws_exception_for_invalid_format_item_from_database(string $value): void
    {
        $this->expectException(InvalidInt8RangeArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP($value);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidFormatItemsFromDatabase(): array
    {
        return [
            'plain string' => ['not-a-valid-range'],
            'missing brackets' => ['1,1000'],
            'incomplete range' => ['[1'],
        ];
    }
}
