<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception\DriverException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBitArrayItemForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class BitArrayTypeTest extends ArrayTypeTestCase
{
    use BitLengthRoundTripTrait;

    protected function getTypeName(): string
    {
        return 'bit[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'BIT[]';
    }

    protected static function getLengthColumnType(): string
    {
        return 'BIT(3)[]';
    }

    /**
     * @return array<string, array{array<int, string|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single zero' => [['0']],
            'single one' => [['1']],
            'multiple single bits' => [['0', '1', '0']],
            'array with null item' => [['1', null, '0']],
            'empty array' => [[]],
        ];
    }

    /**
     * @return array<string, array{array<int, string|null>}>
     */
    public static function provideLengthRoundTripValues(): array
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

    #[DataProvider('provideInvalidBitArrayItems')]
    #[Test]
    public function rejects_invalid_bit_array_items(array $inputValue): void
    {
        $this->expectException(InvalidBitArrayItemForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $inputValue);
    }

    /**
     * @return array<string, array{array<int, mixed>}>
     */
    public static function provideInvalidBitArrayItems(): array
    {
        return [
            'alphabetic string' => [['abc']],
            'digit two' => [['2']],
            'with space' => [['1 0']],
            'empty string' => [['']],
        ];
    }
}
