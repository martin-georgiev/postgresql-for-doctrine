<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLqueryArrayItemForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class LqueryArrayTypeTest extends ArrayTypeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->ensurePostgresExtensionInSchema('ltree');
    }

    protected function getTypeName(): string
    {
        return 'lquery[]';
    }

    /**
     * @return array<string, array{array<int, string|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single pattern' => [['Top.*']],
            'multiple patterns' => [['Top.*', '!football|tennis', '*{1,2}']],
            'pattern with a quantifier comma' => [['a.*{1,2}']],
            'array with null item' => [['Top.*', null, '*']],
        ];
    }

    #[DataProvider('provideInvalidItems')]
    #[Test]
    public function rejects_invalid_item(mixed $value): void
    {
        $this->expectException(InvalidLqueryArrayItemForDatabaseException::class);

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
            'consecutive dots' => ['Top..Child'],
            'integer value' => [123],
        ];
    }
}
