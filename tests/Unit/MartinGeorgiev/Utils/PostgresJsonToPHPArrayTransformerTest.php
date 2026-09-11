<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Utils;

use MartinGeorgiev\Utils\Exception\InvalidJsonFormatException;
use MartinGeorgiev\Utils\PostgresJsonToPHPArrayTransformer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PostgresJsonToPHPArrayTransformerTest extends TestCase
{
    #[DataProvider('provideValidJsonTransformations')]
    #[Test]
    public function converts_json_to_php_value(array|bool|int|string|null $phpValue, string $postgresValue): void
    {
        $this->assertSame($phpValue, PostgresJsonToPHPArrayTransformer::transformPostgresJsonEncodedValueToPHPValue($postgresValue));
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

    #[Test]
    public function throws_exception_for_invalid_json(): void
    {
        $this->expectException(InvalidJsonFormatException::class);
        $this->expectExceptionMessage('Invalid JSON format: the value is not decodable JSON');
        PostgresJsonToPHPArrayTransformer::transformPostgresJsonEncodedValueToPHPValue('{invalid json}');
    }
}
