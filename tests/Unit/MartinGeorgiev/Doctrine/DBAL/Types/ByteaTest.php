<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Bytea;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBytesForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBytesForPHPException;
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
    public function converts_empty_string_from_database_to_null(): void
    {
        $this->assertNull($this->fixture->convertToPHPValue('', $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(?string $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(?string $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{phpValue: string|null, postgresValue: string|null}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'null' => [
                'phpValue' => null,
                'postgresValue' => null,
            ],
            'simple ascii string' => [
                'phpValue' => 'hello',
                'postgresValue' => '\\x68656c6c6f',
            ],
            'binary data with null byte and high byte' => [
                'phpValue' => "\x00\xFF",
                'postgresValue' => '\\x00ff',
            ],
            'deadbeef bytes' => [
                'phpValue' => "\xDE\xAD\xBE\xEF",
                'postgresValue' => '\\xdeadbeef',
            ],
            'single null byte' => [
                'phpValue' => "\x00",
                'postgresValue' => '\\x00',
            ],
        ];
    }

    #[Test]
    public function converts_resource_to_binary_string(): void
    {
        $stream = \fopen('php://memory', 'r+');
        \assert(\is_resource($stream));
        \fwrite($stream, 'hello');
        \rewind($stream);

        $this->assertSame('hello', $this->fixture->convertToPHPValue($stream, $this->platform));

        \fclose($stream);
    }

    #[Test]
    public function converts_empty_resource_to_null(): void
    {
        $stream = \fopen('php://memory', 'r+');
        \assert(\is_resource($stream));

        $this->assertNull($this->fixture->convertToPHPValue($stream, $this->platform));

        \fclose($stream);
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidBytesForDatabaseException::class);

        $this->fixture->convertToDatabaseValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'integer input' => [42],
            'array input' => [['not', 'a', 'string']],
            'object input' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidBytesForPHPException::class);

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
            'array input' => [['not', 'a', 'string']],
            'object input' => [new \stdClass()],
            'boolean input' => [true],
            'string without hex prefix' => ['hello'],
            'string with invalid hex content' => ['\\xZZZZ'],
        ];
    }
}
