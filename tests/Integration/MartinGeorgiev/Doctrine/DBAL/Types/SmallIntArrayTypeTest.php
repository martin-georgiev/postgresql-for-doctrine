<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class SmallIntArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'smallint[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'SMALLINT[]';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_array_values(string $testName, array $arrayValue): void
    {
        parent::can_handle_array_values($testName, $arrayValue);
    }

    /**
     * @return array<string, array{string, array<int, int>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'smallint array with positive values' => ['smallint array with positive values', [1, 2, 3, 4, 5]],
            'smallint array with negative values' => ['smallint array with negative values', [-100, -50, 0, 50, 100]],
            'smallint array with max values' => ['smallint array with max values', [-32768, 0, 32767]],
            'smallint array with zeros' => ['smallint array with zeros', [0, 0, 0]],
            'empty smallint array' => ['empty smallint array', []],
        ];
    }
}
