<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Utils;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidJsonArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidJsonItemForPHPException;
use MartinGeorgiev\Utils\PostgresJsonToPHPArrayTransformer;
use PHPUnit\Framework\TestCase;

class PostgresJsonToPHPArrayTransformerTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider provideValidJsonTransformations
     */
    public function can_transform_json_to_php_value(null|array|bool|int|string $phpValue, string $postgresValue): void
    {
        self::assertEquals($phpValue, PostgresJsonToPHPArrayTransformer::transformPostgresJsonEncodedValueToPHPValue($postgresValue));
    }

    /**
     * @return array<string, array{phpValue: mixed, postgresValue: string}>
     */
    public static function provideValidJsonTransformations(): array
    {
        return [
            'simple object' => [
                'phpValue' => ['key' => 'value'],
                'postgresValue' => '{"key":"value"}',
            ],
            'nested object' => [
                'phpValue' => ['key' => ['nested' => 'value']],
                'postgresValue' => '{"key":{"nested":"value"}}',
            ],
            'array' => [
                'phpValue' => [1, 2, 3],
                'postgresValue' => '[1,2,3]',
            ],
            'string' => [
                'phpValue' => 'string',
                'postgresValue' => '"string"',
            ],
            'number' => [
                'phpValue' => 123,
                'postgresValue' => '123',
            ],
            'boolean' => [
                'phpValue' => true,
                'postgresValue' => 'true',
            ],
            'null' => [
                'phpValue' => null,
                'postgresValue' => 'null',
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideValidJsonbArrayTransformations
     */
    public function can_transform_json_array_to_php_array(array $phpArray, string $postgresArray): void
    {
        self::assertEquals($phpArray, PostgresJsonToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray));
    }

    /**
     * @return array<string, array{phpArray: array, postgresArray: string}>
     */
    public static function provideValidJsonbArrayTransformations(): array
    {
        return [
            'empty array' => [
                'phpArray' => [],
                'postgresArray' => '{}',
            ],
            'array with one object' => [
                'phpArray' => ['{key:value}'],
                'postgresArray' => '{{key:value}}',
            ],
            'array with multiple objects' => [
                'phpArray' => ['{key1:value1}', '{key2:value2}'],
                'postgresArray' => '{{key1:value1},{key2:value2}}',
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideValidJsonbArrayItemTransformations
     */
    public function can_transform_json_array_item_to_php_array(array $phpArray, string $item): void
    {
        self::assertEquals($phpArray, PostgresJsonToPHPArrayTransformer::transformPostgresJsonEncodedValueToPHPArray($item));
    }

    /**
     * @return array<string, array{phpArray: array, item: string}>
     */
    public static function provideValidJsonbArrayItemTransformations(): array
    {
        return [
            'simple object' => [
                'phpArray' => ['key' => 'value'],
                'item' => '{"key":"value"}',
            ],
            'nested object' => [
                'phpArray' => ['key' => ['nested' => 'value']],
                'item' => '{"key":{"nested":"value"}}',
            ],
        ];
    }

    /**
     * @test
     */
    public function throws_exception_for_invalid_json(): void
    {
        $this->expectException(InvalidJsonItemForPHPException::class);
        $this->expectExceptionMessage("Postgres value must be single, valid JSON object, '{invalid json}' given");
        PostgresJsonToPHPArrayTransformer::transformPostgresJsonEncodedValueToPHPValue('{invalid json}');
    }

    /**
     * @test
     */
    public function throws_exception_for_invalid_json_array_item(): void
    {
        $this->expectException(InvalidJsonArrayItemForPHPException::class);
        $this->expectExceptionMessage("Invalid JSON format in array: '{invalid json}'");
        PostgresJsonToPHPArrayTransformer::transformPostgresJsonEncodedValueToPHPArray('{invalid json}');
    }

    /**
     * @test
     */
    public function throws_exception_for_non_array_json_array_item(): void
    {
        $this->expectException(InvalidJsonArrayItemForPHPException::class);
        $this->expectExceptionMessage('Array values must be valid JSON objects, \'"string"\' given');
        PostgresJsonToPHPArrayTransformer::transformPostgresJsonEncodedValueToPHPArray('"string"');
    }
}
