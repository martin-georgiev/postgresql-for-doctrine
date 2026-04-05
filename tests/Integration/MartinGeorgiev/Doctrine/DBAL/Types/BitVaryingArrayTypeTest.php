<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBitVaryingArrayItemForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class BitVaryingArrayTypeTest extends ArrayTypeTestCase
{
    use BitLengthRoundTripTrait;

    protected function getTypeName(): string
    {
        return 'bit varying[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'BIT VARYING[]';
    }

    protected static function getLengthColumnType(): string
    {
        return 'BIT VARYING(5)[]';
    }

    /**
     * @return array<string, array{array<int, string|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single zero' => [['0']],
            'single one' => [['1']],
            'varying length bits' => [['1', '10', '101']],
            'all zeros' => [['00000000']],
            'all ones' => [['11111111']],
            'array with null item' => [['101', null, '010']],
            'empty array' => [[]],
        ];
    }

    /**
     * @return array<string, array{array<int, string|null>}>
     */
    public static function provideLengthRoundTripValues(): array
    {
        return [
            'short elements' => [['1', '10']],
            'max length elements' => [['10101', '01010']],
            'with null' => [['101', null, '0']],
        ];
    }

    #[DataProvider('provideInvalidBitArrayItems')]
    #[Test]
    public function rejects_invalid_bit_array_items(array $inputValue): void
    {
        $this->expectException(InvalidBitVaryingArrayItemForDatabaseException::class);

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
