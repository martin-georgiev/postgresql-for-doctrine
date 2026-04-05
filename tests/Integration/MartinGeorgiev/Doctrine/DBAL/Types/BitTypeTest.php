<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBitForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class BitTypeTest extends ScalarTypeTestCase
{
    use BitLengthRoundTripTrait;

    protected function getTypeName(): string
    {
        return 'bit';
    }

    protected function getPostgresTypeName(): string
    {
        return 'BIT';
    }

    protected static function getLengthColumnType(): string
    {
        return 'BIT(3)';
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
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideLengthRoundTripValues(): array
    {
        return [
            'exact match 101' => ['101'],
            'three zeros' => ['000'],
            'three ones' => ['111'],
        ];
    }

    #[DataProvider('provideInvalidBitStrings')]
    #[Test]
    public function rejects_invalid_bit_string_before_database_write(string $value): void
    {
        $this->expectException(InvalidBitForDatabaseException::class);

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
