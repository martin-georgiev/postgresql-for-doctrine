<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidJsonItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Jsonb;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class JsonbTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Jsonb $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);

        $this->fixture = new Jsonb();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('jsonb', $this->fixture->getName());
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(array|bool|float|int|string|null $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(array|bool|float|int|string|null $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{
     *     phpValue: array|bool|float|int|string|null,
     *     postgresValue: string|null
     * }>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'null value' => [
                'phpValue' => null,
                'postgresValue' => null,
            ],
            'boolean true' => [
                'phpValue' => true,
                'postgresValue' => 'true',
            ],
            'empty array' => [
                'phpValue' => [],
                'postgresValue' => '[]',
            ],
            'integer value' => [
                'phpValue' => 13,
                'postgresValue' => '13',
            ],
            'float value' => [
                'phpValue' => 13.93,
                'postgresValue' => '13.93',
            ],
            'string value' => [
                'phpValue' => 'a string value',
                'postgresValue' => '"a string value"',
            ],
            'numeric array' => [
                'phpValue' => [681, 1185, 1878, 1989],
                'postgresValue' => '[681,1185,1878,1989]',
            ],
            'complex object' => [
                'phpValue' => [
                    'key1' => 'value1',
                    'key2' => false,
                    'key3' => '15',
                    'key4' => 15,
                    'key5' => [112, 242, 309, 310],
                ],
                'postgresValue' => '{"key1":"value1","key2":false,"key3":"15","key4":15,"key5":[112,242,309,310]}',
            ],
        ];
    }

    #[Test]
    public function throws_exception_for_non_encodable_value(): void
    {
        $resourceThatCannotBeJsonEncoded = \fopen('php://memory', 'r');
        if ($resourceThatCannotBeJsonEncoded === false) {
            $this->fail('Failed to create test resource');
        }

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage("can't be resolved to valid JSON");

        try {
            // @phpstan-ignore-next-line argument.type - Testing invalid type handling
            $this->fixture->convertToDatabaseValue($resourceThatCannotBeJsonEncoded, $this->platform);
        } finally {
            \fclose($resourceThatCannotBeJsonEncoded);
        }
    }

    #[Test]
    public function throws_exception_for_circular_reference(): void
    {
        // Create a circular reference
        $object1 = new \stdClass();
        $object2 = new \stdClass();
        $object1->reference = $object2;
        $object2->reference = $object1;

        // Set up a custom error handler to expect the warning
        $warningTriggered = false;
        \set_error_handler(static function ($error, $errorText) use (&$warningTriggered): bool {
            if ($error === E_WARNING && \str_contains($errorText, 'var_export does not handle circular references')) {
                $warningTriggered = true;

                return true; // Suppress the circular reference warning
            }

            return false; // Let other errors/warnings through
        });

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage("can't be resolved to valid JSON");

        try {
            // @phpstan-ignore-next-line argument.type - Testing invalid type handling
            $this->fixture->convertToDatabaseValue($object1, $this->platform);
        } finally {
            \restore_error_handler();
        }

        $this->assertTrue($warningTriggered, 'Expected warning about circular references was not triggered');
    }

    #[DataProvider('provideInvalidJsonStrings')]
    #[Test]
    public function throws_exception_for_invalid_json_strings(string $invalidJson): void
    {
        $this->expectException(InvalidJsonItemForPHPException::class);
        $this->expectExceptionMessage('Postgres value must be single, valid JSON object');

        $this->fixture->convertToPHPValue($invalidJson, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidJsonStrings(): array
    {
        return [
            'invalid json syntax' => ['{invalid json}'],
            'empty string' => [''],
            'malformed json' => ['{"key": value}'],
            'incomplete json' => ['{"key":'],
            'trailing comma' => ['{"key": "value",}'],
            'unquoted keys' => ['{key: "value"}'],
            'single quotes' => ["{'key': 'value'}"],
        ];
    }
}
