<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Utils;

use MartinGeorgiev\Utils\PreciseFloatFormatter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PreciseFloatFormatterTest extends TestCase
{
    #[DataProvider('provideFloats')]
    #[Test]
    public function preserves_full_float_precision(float $value, string $expected): void
    {
        $this->assertSame($expected, PreciseFloatFormatter::format($value));
    }

    /**
     * @return array<string, array{float, string}>
     */
    public static function provideFloats(): array
    {
        return [
            'short form that reads back as the same float' => [1.5, '1.5'],
            'whole number' => [1.0, '1'],
            'negative fraction' => [-0.1, '-0.1'],
            'large exponent' => [1.0E+25, '1.0E+25'],
            '17 significant digits' => [0.30000000000000004, '0.30000000000000004'],
            'more digits than the precision setting keeps' => [12345678.901234567, '12345678.901234567'],
        ];
    }
}
