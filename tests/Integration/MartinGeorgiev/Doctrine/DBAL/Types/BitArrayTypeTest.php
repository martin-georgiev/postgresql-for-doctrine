<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception\DriverException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBitArrayItemForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class BitArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'bit[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'BIT[]';
    }

    /**
     * @return array<string, array{string, array<int, string|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single zero' => ['single zero', ['0']],
            'single one' => ['single one', ['1']],
            'multiple single bits' => ['multiple single bits', ['0', '1', '0']],
            'array with null item' => ['array with null item', ['1', null, '0']],
            'empty array' => ['empty array', []],
        ];
    }

    #[DataProvider('provideFixedLengthTransformations')]
    #[Test]
    public function can_round_trip_with_fixed_element_length(array $inputValue): void
    {
        $tableName = 'test_type_bit_array_fixed';
        $columnName = 'test_column';
        $this->createTestTableForDataType($tableName, $columnName, 'BIT(3)[]');

        try {
            $this->connection->createQueryBuilder()
                ->insert(self::DATABASE_SCHEMA.'.'.$tableName)
                ->values([$columnName => ':value'])
                ->setParameter('value', $inputValue, $this->getTypeName())
                ->executeStatement();

            $retrieved = $this->fetchConvertedValue($this->getTypeName(), $tableName, $columnName);
            $this->assertSame($inputValue, $retrieved);
        } finally {
            $this->dropTestTableIfItExists($tableName);
        }
    }

    /**
     * @return array<string, array{array<int, string|null>}>
     */
    public static function provideFixedLengthTransformations(): array
    {
        return [
            'single element' => [['101']],
            'multiple elements' => [['101', '110', '000']],
            'with null' => [['101', null, '010']],
        ];
    }

    #[DataProvider('provideMultiBitValuesRejectedByDefaultLength')]
    #[Test]
    public function rejects_multi_bit_values_in_default_bit_array(array $inputValue): void
    {
        $this->expectException(DriverException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $inputValue);
    }

    /**
     * @return array<string, array{array<int, string|null>}>
     */
    public static function provideMultiBitValuesRejectedByDefaultLength(): array
    {
        return [
            'multi-bit values' => [['10110', '00001']],
            'all zeros byte' => [['00000000']],
            'all ones byte' => [['11111111']],
            'three-bit values with null' => [['101', null, '010']],
        ];
    }

    #[Test]
    public function rejects_invalid_bit_string(): void
    {
        $this->expectException(InvalidBitArrayItemForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), ['abc']);
    }

    #[Test]
    public function rejects_non_bit_digit(): void
    {
        $this->expectException(InvalidBitArrayItemForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), ['2']);
    }
}
