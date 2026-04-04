<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Bit;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBitForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBitForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BitTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Bit $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Bit();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('bit', $this->fixture->getName());
    }

    #[Test]
    public function returns_bit_without_length_by_default(): void
    {
        $this->assertSame('BIT', $this->fixture->getSQLDeclaration([], $this->platform));
    }

    #[Test]
    public function returns_bit_with_length_when_specified(): void
    {
        $this->assertSame('BIT(3)', $this->fixture->getSQLDeclaration(['length' => 3], $this->platform));
    }

    #[Test]
    public function returns_bit_with_length_one_for_minimum(): void
    {
        $this->assertSame('BIT(1)', $this->fixture->getSQLDeclaration(['length' => 1], $this->platform));
    }

    #[Test]
    public function converts_null_to_database_value(): void
    {
        $this->assertNull($this->fixture->convertToDatabaseValue(null, $this->platform));
    }

    #[Test]
    public function converts_null_to_php_value(): void
    {
        $this->assertNull($this->fixture->convertToPHPValue(null, $this->platform));
    }

    #[Test]
    public function converts_empty_string_from_database_to_null(): void
    {
        $this->assertNull($this->fixture->convertToPHPValue('', $this->platform));
    }

    #[DataProvider('provideValidBitStrings')]
    #[Test]
    public function can_round_trip_valid_bit_strings(string $bitString): void
    {
        $this->assertSame($bitString, $this->fixture->convertToDatabaseValue($bitString, $this->platform));
        $this->assertSame($bitString, $this->fixture->convertToPHPValue($bitString, $this->platform));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideValidBitStrings(): array
    {
        return [
            'single zero' => ['0'],
            'single one' => ['1'],
            'mixed bits' => ['10110'],
            'all zeros' => ['00000000'],
            'all ones' => ['11111111'],
        ];
    }

    #[DataProvider('provideInvalidFormatStrings')]
    #[Test]
    public function throws_exception_for_invalid_format_in_database_value(string $value): void
    {
        $this->expectException(InvalidBitForDatabaseException::class);

        $this->fixture->convertToDatabaseValue($value, $this->platform);
    }

    #[DataProvider('provideInvalidFormatStrings')]
    #[Test]
    public function throws_exception_for_invalid_format_in_php_value(string $value): void
    {
        $this->expectException(InvalidBitForPHPException::class);

        $this->fixture->convertToPHPValue($value, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidFormatStrings(): array
    {
        return [
            'digit two' => ['2'],
            'alphabetic' => ['abc'],
            'with space' => ['1 0'],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidBitForPHPException::class);

        $this->fixture->convertToPHPValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'integer input' => [42],
            'float input' => [3.14],
            'array input' => [['0', '1']],
            'object input' => [new \stdClass()],
            'boolean input' => [true],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidBitForDatabaseException::class);

        $this->fixture->convertToDatabaseValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'integer input' => [42],
            'array input' => [['0', '1']],
        ];
    }
}
