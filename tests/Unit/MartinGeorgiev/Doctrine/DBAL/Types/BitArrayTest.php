<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\BitArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBitArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBitArrayItemForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BitArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private BitArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new BitArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('bit[]', $this->fixture->getName());
    }

    #[Test]
    public function returns_bit_array_without_length_by_default(): void
    {
        $this->assertSame('BIT[]', $this->fixture->getSQLDeclaration([], $this->platform));
    }

    #[Test]
    public function returns_bit_array_with_element_length_when_specified(): void
    {
        $this->assertSame('BIT(3)[]', $this->fixture->getSQLDeclaration(['length' => 3], $this->platform));
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
        $this->assertSame($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{
     *     phpValue: array|null,
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
            'single zero' => [
                'phpValue' => ['0'],
                'postgresValue' => '{"0"}',
            ],
            'single one' => [
                'phpValue' => ['1'],
                'postgresValue' => '{"1"}',
            ],
            'mixed bits' => [
                'phpValue' => ['10110', '00001'],
                'postgresValue' => '{"10110","00001"}',
            ],
            'all zeros' => [
                'phpValue' => ['00000000'],
                'postgresValue' => '{"00000000"}',
            ],
            'all ones' => [
                'phpValue' => ['11111111'],
                'postgresValue' => '{"11111111"}',
            ],
            'null item in array' => [
                'phpValue' => ['101', null, '010'],
                'postgresValue' => '{"101",NULL,"010"}',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidBitArrayItemForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'digit two' => [['2']],
            'alphabetic' => [['abc']],
            'integer item' => [[123]],
            'empty string' => [['']],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidBitArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidTypeInputs(): array
    {
        return [
            'string instead of array' => ['not-an-array'],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(string $postgresValue): void
    {
        $this->expectException(InvalidBitArrayItemForPHPException::class);
        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'digit two in array' => ['{"2"}'],
            'alphabetic in array' => ['{"abc"}'],
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
            'single zero' => ['0'],
            'single one' => ['1'],
            'mixed bits' => ['10110'],
            'all zeros' => ['00000000'],
            'all ones' => ['11111111'],
            'null value' => [null],
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
            'digit two' => ['2'],
            'alphabetic' => ['abc'],
            'with space' => ['1 0'],
            'integer' => [123],
            'empty string' => [''],
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
        $this->expectException(InvalidBitArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP(123);
    }
}
