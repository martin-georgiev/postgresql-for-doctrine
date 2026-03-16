<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\BitVarying;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBitVaryingForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBitVaryingForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class BitVaryingTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private BitVarying $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new BitVarying();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('bit varying', $this->fixture->getName());
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

    /**
     * @return array<string, array{bitString: string}>
     */
    public static function provideValidBitStrings(): array
    {
        return [
            'single zero' => ['bitString' => '0'],
            'single one' => ['bitString' => '1'],
            'mixed bits' => ['bitString' => '10110'],
            'all zeros' => ['bitString' => '00000000'],
            'all ones' => ['bitString' => '11111111'],
        ];
    }

    #[DataProvider('provideValidBitStrings')]
    #[Test]
    public function can_convert_valid_bit_string_to_database_value(string $bitString): void
    {
        $this->assertSame($bitString, $this->fixture->convertToDatabaseValue($bitString, $this->platform));
    }

    #[DataProvider('provideValidBitStrings')]
    #[Test]
    public function can_convert_valid_bit_string_to_php_value(string $bitString): void
    {
        $this->assertSame($bitString, $this->fixture->convertToPHPValue($bitString, $this->platform));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPhpInputs(): array
    {
        return [
            'integer input' => [42],
            'array input' => [['0', '1']],
            'object input' => [new \stdClass()],
            'boolean true' => [true],
            'boolean false' => [false],
        ];
    }

    #[DataProvider('provideInvalidPhpInputs')]
    #[Test]
    public function throws_exception_for_non_string_php_value(mixed $value): void
    {
        $this->expectException(InvalidBitVaryingForPHPException::class);
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

    #[DataProvider('provideInvalidFormatStrings')]
    #[Test]
    public function throws_exception_for_invalid_format_php_value(string $value): void
    {
        $this->expectException(InvalidBitVaryingForPHPException::class);
        $this->fixture->convertToPHPValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseInputs(): array
    {
        return [
            'integer input' => [42],
            'array input' => [['0', '1']],
        ];
    }

    #[DataProvider('provideInvalidDatabaseInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value(mixed $value): void
    {
        $this->expectException(InvalidBitVaryingForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($value, $this->platform);
    }
}
