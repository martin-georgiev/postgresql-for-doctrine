<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

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

    /**
     * @return array<string, array{array<int, int>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'smallint array with positive values' => [[1, 2, 3, 4, 5]],
            'smallint array with negative values' => [[-100, -50, 0, 50, 100]],
            'smallint array with max values' => [[-32768, 0, 32767]],
            'smallint array with zeros' => [[0, 0, 0]],
            'empty smallint array' => [[]],
        ];
    }
}
