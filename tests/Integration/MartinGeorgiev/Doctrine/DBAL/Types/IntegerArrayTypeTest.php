<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class IntegerArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'integer[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'INTEGER[]';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_array_values(string $testName, array $arrayValue): void
    {
        parent::can_handle_array_values($testName, $arrayValue);
    }

    public static function provideValidTransformations(): array
    {
        return [
            'simple integer array' => ['simple integer array', [1, 2, 3, 4, 5]],
            'integer array with negatives' => ['integer array with negatives', [-1, 0, 1, -100, 100]],
            'integer array with max values' => ['integer array with max values', [2147483647, -2147483648, 0]],
        ];
    }
}
