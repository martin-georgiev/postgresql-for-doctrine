<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

final class NumericArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'numeric[]';
    }

    public static function provideValidTransformations(): array
    {
        return [
            'integer-like numerics' => [['1', '42', '-7']],
            'decimals with trailing zeros' => [['502.00', '505.00', '0.00']],
            'high precision decimals' => [['1.0000000000000000000000000001', '-0.000000001']],
            'array with null item' => [[null, '1.50']],
        ];
    }
}
