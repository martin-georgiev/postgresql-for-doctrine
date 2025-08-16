<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class BigIntArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'bigint[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'BIGINT[]';
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
            'simple bigint array' => ['simple bigint array', [9223372036854775807, 1, -9223372036854775807]],
            'bigint array with zeros' => ['bigint array with zeros', [0, 0, 0, 1, 0]],
            'bigint array with large numbers' => ['bigint array with large numbers', [1000000000000, 2000000000000, 3000000000000]],
            'bigint array with negative numbers' => ['bigint array with negative numbers', [-1000000000000, -2000000000000, -3000000000000]],
            'bigint array with PHP max and min integer constants' => ['bigint array with PHP max and min integer constants', [PHP_INT_MAX, PHP_INT_MIN, 0]],
        ];
    }
}
