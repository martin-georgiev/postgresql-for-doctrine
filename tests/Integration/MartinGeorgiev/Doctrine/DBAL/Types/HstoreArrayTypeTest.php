<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidHstoreArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidHstoreArrayItemForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class HstoreArrayTypeTest extends ArrayTypeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->ensurePostgresExtensionInSchema('hstore');
    }

    protected function getTypeName(): string
    {
        return 'hstore[]';
    }

    /**
     * @return array<string, array{array<int, array<string, string|null>|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'array of simple hstores' => [[['a' => 'b'], ['c' => 'd']]],
            'array with null item' => [[['a' => 'b'], null, ['c' => 'd']]],
            'array with null value in hstore' => [[['key' => null]]],
            'array with empty hstore' => [[[]]],
            'hstore with multiple pairs' => [[['a' => 'b', 'c' => 'd', 'e' => 'f']]],
            'hstore with special characters' => [[['key' => 'value with "quotes" and backslash\\']]],
        ];
    }

    #[Test]
    public function rejects_non_array_value(): void
    {
        $this->expectException(InvalidHstoreArrayItemForPHPException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, 'not-an-array');
    }

    #[DataProvider('provideInvalidArrayItems')]
    #[Test]
    public function rejects_invalid_array_item(mixed $value): void
    {
        $this->expectException(InvalidHstoreArrayItemForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $value);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidArrayItems(): array
    {
        return [
            'integer item' => [[123]],
            'string item' => [['not-an-hstore']],
            'hstore with integer value' => [[['key' => 123]]],
        ];
    }
}
