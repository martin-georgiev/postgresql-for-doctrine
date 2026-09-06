<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidUlidArrayItemForDatabaseException;
use PHPUnit\Framework\Attributes\Test;

final class UlidArrayTypeTest extends ArrayTypeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->ensurePostgresExtensionInSchema('ulid');
    }

    protected function getTypeName(): string
    {
        return 'ulid[]';
    }

    /**
     * @return array<string, array{array<int, string>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single ULID' => [['01ARZ3NDEKTSV4RRFFQ69G5FAV']],
            'multiple ULIDs' => [[
                '01ARZ3NDEKTSV4RRFFQ69G5FAV',
                '01BX5ZZKBKACTAV9WEVGEMMVRZ',
            ]],
            'minimum ULID' => [['00000000000000000000000000']],
            'maximum ULID' => [['7ZZZZZZZZZZZZZZZZZZZZZZZZZ']],
        ];
    }

    #[Test]
    public function rejects_invalid_ulid_item(): void
    {
        $this->expectException(InvalidUlidArrayItemForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, ['invalid-ulid', '01ARZ3NDEKTSV4RRFFQ69G5FAV']);
    }

    #[Test]
    public function roundtrips_array_with_null_elements(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $inputValue = ['01ARZ3NDEKTSV4RRFFQ69G5FAV', null, '01BX5ZZKBKACTAV9WEVGEMMVRZ'];

        $this->runDbalBindingRoundTrip($typeName, $columnType, $inputValue);
    }
}
