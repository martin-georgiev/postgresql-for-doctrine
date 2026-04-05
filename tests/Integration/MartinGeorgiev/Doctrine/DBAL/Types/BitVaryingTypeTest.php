<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBitVaryingForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class BitVaryingTypeTest extends ScalarTypeTestCase
{
    use BitLengthRoundTripTrait;

    protected function getTypeName(): string
    {
        return 'bit varying';
    }

    protected function getPostgresTypeName(): string
    {
        return 'BIT VARYING';
    }

    protected static function getLengthColumnType(): string
    {
        return 'BIT VARYING(5)';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(string $testValue): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $testValue);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single zero' => ['0'],
            'single one' => ['1'],
            'mixed bits' => ['10110'],
            'all zeros' => ['00000000'],
            'all ones' => ['11111111'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideLengthRoundTripValues(): array
    {
        return [
            'short value' => ['00'],
            'exact length' => ['10101'],
            'single bit' => ['1'],
        ];
    }

    #[DataProvider('provideInvalidBitStrings')]
    #[Test]
    public function rejects_invalid_bit_string_before_database_write(string $value): void
    {
        $this->expectException(InvalidBitVaryingForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $value);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidBitStrings(): array
    {
        return [
            'alphabetic' => ['abc'],
            'digit two' => ['2'],
            'with space' => ['1 0'],
        ];
    }
}
