<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Bytea;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidByteaForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidByteaForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ByteaTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Bytea $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Bytea();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('bytea', $this->fixture->getName());
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
     * @return array<string, array{hexInput: string, expectedBinary: string}>
     */
    public static function provideHexFormatDecoding(): array
    {
        return [
            'ASCII string Hello' => ['hexInput' => '\\x48656c6c6f', 'expectedBinary' => 'Hello'],
            'null byte' => ['hexInput' => '\\x00', 'expectedBinary' => "\x00"],
            'empty hex after prefix' => ['hexInput' => '\\x', 'expectedBinary' => ''],
            'binary data' => ['hexInput' => '\\xff00ab', 'expectedBinary' => "\xff\x00\xab"],
        ];
    }

    #[DataProvider('provideHexFormatDecoding')]
    #[Test]
    public function decodes_hex_format_from_database(string $hexInput, string $expectedBinary): void
    {
        $this->assertSame($expectedBinary, $this->fixture->convertToPHPValue($hexInput, $this->platform));
    }

    /**
     * @return array<string, array{rawBinary: string}>
     */
    public static function provideNonHexPassthrough(): array
    {
        return [
            'raw ASCII string' => ['rawBinary' => 'raw binary'],
            'escape format style' => ['rawBinary' => "Hello\x00World"],
            'plain text' => ['rawBinary' => 'some data'],
        ];
    }

    #[DataProvider('provideNonHexPassthrough')]
    #[Test]
    public function passes_through_non_hex_strings_from_database(string $rawBinary): void
    {
        $this->assertSame($rawBinary, $this->fixture->convertToPHPValue($rawBinary, $this->platform));
    }

    /**
     * @return array<string, array{input: string, expectedHex: string}>
     */
    public static function provideValidDatabaseValues(): array
    {
        return [
            'ASCII string' => ['input' => 'Hello', 'expectedHex' => '\\x48656c6c6f'],
            'binary data' => ['input' => "\xff\x00\xab", 'expectedHex' => '\\xff00ab'],
            'null byte' => ['input' => "\x00", 'expectedHex' => '\\x00'],
        ];
    }

    #[DataProvider('provideValidDatabaseValues')]
    #[Test]
    public function encodes_binary_to_hex_for_database(string $input, string $expectedHex): void
    {
        $this->assertSame($expectedHex, $this->fixture->convertToDatabaseValue($input, $this->platform));
    }

    #[Test]
    public function decodes_stream_resource_from_database(): void
    {
        $stream = \fopen('php://memory', 'r+');
        \assert($stream !== false);
        \fwrite($stream, '\\x48656c6c6f');
        \rewind($stream);

        $this->assertSame('Hello', $this->fixture->convertToPHPValue($stream, $this->platform));

        \fclose($stream);
    }

    #[Test]
    public function passes_through_raw_binary_stream_from_database(): void
    {
        $stream = \fopen('php://memory', 'r+');
        \assert($stream !== false);
        \fwrite($stream, 'raw binary');
        \rewind($stream);

        $this->assertSame('raw binary', $this->fixture->convertToPHPValue($stream, $this->platform));

        \fclose($stream);
    }

    #[DataProvider('provideInvalidHexFormats')]
    #[Test]
    public function throws_exception_for_invalid_hex_format(string $value): void
    {
        $this->expectException(InvalidByteaForPHPException::class);

        $this->fixture->convertToPHPValue($value, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidHexFormats(): array
    {
        return [
            'non-hex characters' => ['\\xZZ'],
            'odd-length hex' => ['\\xABC'],
            'mixed invalid' => ['\\xGG00'],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidByteaForPHPException::class);

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
            'array input' => [['data']],
            'object input' => [new \stdClass()],
            'boolean input' => [true],
        ];
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'integer input' => [42],
            'array input' => [['data']],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidByteaForDatabaseException::class);

        $this->fixture->convertToDatabaseValue($value, $this->platform);
    }
}
