<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLtxtqueryArrayItemForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class LtxtqueryArrayTypeTest extends ArrayTypeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->ensurePostgresExtensionInSchema('ltree');
    }

    protected function getTypeName(): string
    {
        return 'ltxtquery[]';
    }

    /**
     * @return array<string, array{array<int, string|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single query' => [['Earth & Moon']],
            'multiple queries' => [['Earth & Moon', '!Transportation', '( a | b ) & c']],
            'array with null item' => [['Earth', null, '!Asia']],
        ];
    }

    #[DataProvider('provideInvalidItems')]
    #[Test]
    public function rejects_invalid_item(mixed $value): void
    {
        $this->expectException(InvalidLtxtqueryArrayItemForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, [$value]);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidItems(): array
    {
        return [
            'empty string' => [''],
            'dotted path' => ['a.b'],
            'integer value' => [123],
        ];
    }
}
