<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class RealArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'real[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'REAL[]';
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
            'simple real array' => ['simple real array', [1.5, 2.5, 3.5]],
            'real array with negative values' => ['real array with negative values', [-1.5, -2.5, -3.5]],
            'real array with high precision' => ['real array with high precision', [
                1.123457,
                2.987654,
                3.141593,
            ]],
            'real array with integers' => ['real array with integers', [1.0, 2.0, 3.0]],
            'real array with zero' => ['real array with zero', [0.0, 1.5, -1.5]],
            'empty real array' => ['empty real array', []],
            'real array with large numbers' => ['real array with large numbers', [3.402823e+6, -3.402823e+6]],
        ];
    }
}
