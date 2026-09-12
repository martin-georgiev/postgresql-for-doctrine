<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\Test;

final class RealArrayTypeTest extends FloatArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'real[]';
    }

    /**
     * @return array<string, array{array<int, float>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple real array' => [[1.5, 2.5, 3.5]],
            'real array with negative values' => [[-1.5, -2.5, -3.5]],
            'real array with high precision' => [[
                1.123457,
                2.987654,
                3.141593,
            ]],
            'real array with integers' => [[1.0, 2.0, 3.0]],
            'real array with zero' => [[0.0, 1.5, -1.5]],
            'empty real array' => [[]],
            'real array with large numbers' => [[3.402823e+6, -3.402823e+6]],
        ];
    }

    public static function providePostgresWrittenValues(): array
    {
        return [
            'shortest round-trip form needing more digits than the type guarantees' => [
                'literal' => '{1.1234567,0.12345679}',
                'expected' => [1.1234567, 0.12345679],
            ],
            'subnormal' => [
                'literal' => '{1e-45}',
                'expected' => [1.0E-45],
            ],
            'scientific notation at the upper bound' => [
                'literal' => '{3.4028235e+38,-3.4028235e+38}',
                'expected' => [3.4028235E+38, -3.4028235E+38],
            ],
        ];
    }

    #[Test]
    public function roundtrips_non_finite_values(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();
        $this->runDbalBindingRoundTrip($typeName, $columnType, [\INF, -\INF, 1.5]);
    }
}
