<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

final class DoublePrecisionArrayTypeTest extends FloatArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'double precision[]';
    }

    /**
     * @return array<string, array{array<int, float>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple double precision array' => [[1.123456789, 2.123456789, 3.123456789]],
            'double precision array with negative values' => [[-1.5, -2.5, -3.5]],
            'double precision array with high precision' => [[
                1.1234567890123,
                2.9876543210988,
                3.1415926535898,
            ]],
            'double precision array with integers' => [[1.0, 2.0, 3.0]],
            'double precision array with zero' => [[0.0, 1.5, -1.5]],
            'empty double precision array' => [[]],
            'double precision array with large numbers' => [[1234567.123456, -9876543.987654]],
        ];
    }

    public static function providePostgresWrittenValues(): array
    {
        return [
            'shortest round-trip form needing more digits than the type guarantees' => [
                'literal' => '{0.12345678901234566}',
                'expected' => [0.12345678901234566],
            ],
            'subnormal' => [
                'literal' => '{5e-324}',
                'expected' => [5.0E-324],
            ],
            'scientific notation at the upper bound' => [
                'literal' => '{1.7976931348623157e+308,-1.7976931348623157e+308}',
                'expected' => [1.7976931348623157E+308, -1.7976931348623157E+308],
            ],
        ];
    }
}
