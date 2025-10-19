<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class DoublePrecisionArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'double precision[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'DOUBLE PRECISION[]';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_array_values(string $testName, array $arrayValue): void
    {
        parent::can_handle_array_values($testName, $arrayValue);
    }

    /**
     * @return array<string, array{string, array<int, float>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple double precision array' => ['simple double precision array', [1.123456789, 2.123456789, 3.123456789]],
            'double precision array with negative values' => ['double precision array with negative values', [-1.5, -2.5, -3.5]],
            'double precision array with high precision' => ['double precision array with high precision', [
                1.1234567890123,
                2.9876543210988,
                3.1415926535898,
            ]],
            'double precision array with integers' => ['double precision array with integers', [1.0, 2.0, 3.0]],
            'double precision array with zero' => ['double precision array with zero', [0.0, 1.5, -1.5]],
            'empty double precision array' => ['empty double precision array', []],
            'double precision array with large numbers' => ['double precision array with large numbers', [1234567.123456, -9876543.987654]],
        ];
    }
}
