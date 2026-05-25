<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidHstoreForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class HstoreTypeTest extends ScalarTypeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->ensurePostgresExtensionInSchema('hstore');
    }

    protected function getTypeName(): string
    {
        return 'hstore';
    }

    #[DataProvider('provideValidRoundTrips')]
    #[Test]
    public function roundtrips_value(array $value): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $value);
    }

    /**
     * @return array<string, array{array<string, string|null>}>
     */
    public static function provideValidRoundTrips(): array
    {
        return [
            'simple key value' => [['key' => 'value', 'count' => '42']],
            'null values in hstore' => [['a' => 'b', 'c' => null]],
            'special characters' => [['key' => 'value with "quotes" and backslash\\']],
        ];
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
