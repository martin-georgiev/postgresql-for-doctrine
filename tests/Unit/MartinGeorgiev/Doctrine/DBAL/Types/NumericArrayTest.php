<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidNumericArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidNumericArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\NumericArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class NumericArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&Stub
     */
    private Stub $platform;

    private NumericArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createStub(AbstractPlatform::class);
        $this->fixture = new NumericArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('numeric[]', $this->fixture->getName());
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_database_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_php_value(?array $phpValue, ?string $postgresValue): void
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
            'single numeric' => [
                'phpValue' => ['1.50'],
                'postgresValue' => '{"1.50"}',
            ],
            'multiple numerics' => [
                'phpValue' => ['1.50', '-2.75', '42'],
                'postgresValue' => '{"1.50","-2.75","42"}',
            ],
            'high precision decimal' => [
                'phpValue' => ['1.0000000000000000000000000001'],
                'postgresValue' => '{"1.0000000000000000000000000001"}',
            ],
            'array with null item' => [
                'phpValue' => [null, '1.50'],
                'postgresValue' => '{NULL,"1.50"}',
            ],
        ];
    }

    #[Test]
    public function preserves_exact_precision_for_unquoted_database_values(): void
    {
        $postgresValue = '{502.00,505.00,0.00,-0.10,1.0000000000000000000000000001}';
        $expectedResult = ['502.00', '505.00', '0.00', '-0.10', '1.0000000000000000000000000001'];

        $result = $this->fixture->convertToPHPValue($postgresValue, $this->platform);

        $this->assertSame($expectedResult, $result);
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidNumericArrayItemForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'empty string item' => [['']],
            'non-numeric string item' => [['abc']],
            'integer item' => [[123]],
            'float item' => [[1.5]],
            'boolean item' => [[true]],
            'scientific notation item' => [['1.5e3']],
            'leading plus sign item' => [['+1.5']],
            'NaN item' => [['NaN']],
            'mixed valid and invalid' => [['1.50', 'not-a-number']],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidNumericArrayItemForPHPException::class);
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

    #[DataProvider('provideValidArrayItemsForDatabase')]
    #[Test]
    public function validates_valid_array_item_for_database(mixed $value): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase($value));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideValidArrayItemsForDatabase(): array
    {
        return [
            'integer-like string' => ['42'],
            'negative integer-like string' => ['-7'],
            'decimal string' => ['1.50'],
            'high precision decimal string' => ['1.0000000000000000000000000001'],
            'null value' => [null],
        ];
    }

    #[DataProvider('provideInvalidArrayItemsForDatabase')]
    #[Test]
    public function validates_invalid_array_item_for_database(mixed $value): void
    {
        $this->assertFalse($this->fixture->isValidArrayItemForDatabase($value));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidArrayItemsForDatabase(): array
    {
        return [
            'empty string' => [''],
            'non-numeric string' => ['abc'],
            'scientific notation' => ['1.5e3'],
            'leading plus sign' => ['+1.5'],
            'missing integer part' => ['.5'],
            'trailing dot' => ['5.'],
            'NaN' => ['NaN'],
            'comma as decimal separator' => ['1,5'],
            'trailing newline' => ["1.50\n"],
            'integer' => [123],
            'float' => [3.14],
            'boolean' => [true],
            'object' => [new \stdClass()],
        ];
    }

    #[Test]
    public function converts_null_item_to_php_value(): void
    {
        $this->assertNull($this->fixture->transformArrayItemForPHP(null));
    }

    #[Test]
    public function throws_exception_for_non_string_item_from_database(): void
    {
        $this->expectException(InvalidNumericArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP(123);
    }
}
