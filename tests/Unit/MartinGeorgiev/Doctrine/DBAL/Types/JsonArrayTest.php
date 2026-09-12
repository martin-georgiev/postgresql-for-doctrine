<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidJsonArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidJsonArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\JsonArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class JsonArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&Stub
     */
    private Stub $platform;

    private JsonArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createStub(AbstractPlatform::class);
        $this->fixture = new JsonArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('json[]', $this->fixture->getName());
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_database_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{
     *     phpValue: array|null,
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
            'multiple json objects' => [
                'phpValue' => [
                    [
                        'key1' => 'value1',
                        'key2' => false,
                        'key3' => '15',
                        'key4' => 15,
                        'key5' => [112, 242, 309, 310],
                    ],
                    [
                        'key1' => 'value2',
                        'key2' => true,
                        'key3' => '115',
                        'key4' => 115,
                        'key5' => [304, 404, 504, 604],
                    ],
                ],
                'postgresValue' => '{"{\"key1\":\"value1\",\"key2\":false,\"key3\":\"15\",\"key4\":15,\"key5\":[112,242,309,310]}","{\"key1\":\"value2\",\"key2\":true,\"key3\":\"115\",\"key4\":115,\"key5\":[304,404,504,604]}"}',
            ],
            'array with only a null item' => [
                'phpValue' => [null],
                'postgresValue' => '{NULL}',
            ],
            'array with null items among json values' => [
                'phpValue' => [null, ['key' => 'value'], null, 1],
                'postgresValue' => '{NULL,"{\"key\":\"value\"}",NULL,"1"}',
            ],
        ];
    }

    #[DataProvider('provideStoredNullElementRepresentations')]
    #[Test]
    public function converts_every_stored_null_element_representation_to_php_null(string $postgresValue): void
    {
        $this->assertSame([null], $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * Guards the readability of rows written before a null item became a SQL NULL element.
     *
     * @return array<string, array{string}>
     */
    public static function provideStoredNullElementRepresentations(): array
    {
        return [
            'sql null element' => ['{NULL}'],
            'json null element stored by the previous behaviour' => ['{"null"}'],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidJsonArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidTypeInputs(): array
    {
        return [
            'string instead of array' => ['not-an-array'],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(array $phpValue): void
    {
        $this->expectException(InvalidJsonArrayItemForDatabaseException::class);
        $this->expectExceptionMessage('Array items must be convertible to JSON');

        $this->fixture->convertToDatabaseValue($phpValue, $this->platform);
    }

    /**
     * @return array<string, array{array}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'item containing NAN' => [[['key' => \NAN]]],
            'item containing INF' => [[['key' => \INF]]],
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
            'associative array' => [['key' => 'value']],
            'indexed array' => [[1, 2, 3]],
            'nested array' => [['nested' => ['a', 'b']]],
            'null' => [null],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(string $postgresValue): void
    {
        $this->expectException(InvalidJsonArrayItemForPHPException::class);
        $this->expectExceptionMessage('Invalid JSON format in array');

        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'non-array json' => ['"a string encoded as json"'],
            'invalid json format' => ['{invalid json}'],
        ];
    }
}
