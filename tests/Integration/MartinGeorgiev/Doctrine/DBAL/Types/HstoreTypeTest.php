<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidHstoreForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class HstoreTypeTest extends ScalarTypeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        try {
            $this->connection->executeStatement('CREATE EXTENSION IF NOT EXISTS hstore');
            $this->connection->executeStatement(\sprintf('ALTER EXTENSION hstore SET SCHEMA %s', self::DATABASE_SCHEMA));
        } catch (\Throwable) {
            $this->markTestSkipped('hstore extension is not available');
        }
    }

    protected function getTypeName(): string
    {
        return 'hstore';
    }

    #[Test]
    public function can_handle_simple_key_value(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, ['key' => 'value', 'count' => '42']);
    }

    #[Test]
    public function can_handle_null_values_in_hstore(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, ['a' => 'b', 'c' => null]);
    }

    #[Test]
    public function can_handle_hstore_with_special_characters(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, ['key' => 'value with "quotes" and backslash\\']);
    }

    #[DataProvider('provideInvalidValues')]
    #[Test]
    public function rejects_invalid_value(mixed $value): void
    {
        $this->expectException(InvalidHstoreForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $value);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidValues(): array
    {
        return [
            'string instead of array' => ['not-an-array'],
            'integer instead of array' => [42],
            'array with integer value' => [['key' => 123]],
            'array with object value' => [['key' => new \stdClass()]],
        ];
    }
}
