<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBitVaryingForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class BitVaryingTypeTest extends ScalarTypeTestCase
{
    use BitLengthRoundTripTrait;

    protected function getTypeName(): string
    {
        return 'bit varying';
    }

    protected static function getFieldDeclarationForLengthRoundTrip(): array
    {
        return ['length' => 5];
    }

    protected static function getValueExceedingLength(): string
    {
        return '101010';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function roundtrips_value(string $testValue): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $testValue);
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

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $value);
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
