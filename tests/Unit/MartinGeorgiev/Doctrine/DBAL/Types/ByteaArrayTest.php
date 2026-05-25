<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\ByteaArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBytesArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBytesArrayItemForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ByteaArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private ByteaArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new ByteaArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('bytea[]', $this->fixture->getName());
    }

    /**
     * @param array<int, string|null>|null $phpValue
     */
    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_database_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    /**
     * @param array<int, string|null>|null $phpValue
     */
    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{
     *     phpValue: array<int, string|null>|null,
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
            'simple ascii string' => [
                'phpValue' => ['hello'],
                'postgresValue' => '{"\\\\x68656c6c6f"}',
            ],
            'multiple strings' => [
                'phpValue' => ['hello', 'world'],
                'postgresValue' => '{"\\\\x68656c6c6f","\\\\x776f726c64"}',
            ],
            'null item in array' => [
                'phpValue' => ['hello', null, 'world'],
                'postgresValue' => '{"\\\\x68656c6c6f",NULL,"\\\\x776f726c64"}',
            ],
            'empty string item' => [
                'phpValue' => [''],
                'postgresValue' => '{"\\\\x"}',
            ],
            'binary data with null byte and high byte' => [
                'phpValue' => ["\x00\xFF"],
                'postgresValue' => '{"\\\\x00ff"}',
            ],
            'mixed binary and ascii' => [
                'phpValue' => ['hello', "\xDE\xAD\xBE\xEF"],
                'postgresValue' => '{"\\\\x68656c6c6f","\\\\xdeadbeef"}',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidBytesArrayItemForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'integer item' => [[123]],
            'float item' => [[3.14]],
            'object item' => [[new \stdClass()]],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidBytesArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidTypeInputs(): array
    {
        return [
            'string instead of array' => ['not-an-array'],
            'integer instead of array' => [42],
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
            'ascii string' => ['binary string'],
            'empty string' => [''],
            'binary string with null byte' => ["\x00\xFF"],
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
            'integer' => [123],
            'float' => [3.14],
            'object' => [new \stdClass()],
            'array' => [[]],
            'boolean' => [true],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(string $postgresValue): void
    {
        $this->expectException(InvalidBytesArrayItemForPHPException::class);
        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'non-string item' => ['{42}'],
            'without hex prefix' => ['{"hello"}'],
            'invalid hex content' => ['{"\\\\xZZZZ"}'],
        ];
    }
}
