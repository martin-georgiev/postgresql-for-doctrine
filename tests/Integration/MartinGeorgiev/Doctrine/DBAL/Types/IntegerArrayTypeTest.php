<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidIntegerArrayItemForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class IntegerArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'integer[]';
    }

    /**
     * @return array<string, array{array<int, int>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple integer array' => [[1, 2, 3, 4, 5]],
            'integer array with negatives' => [[-1, 0, 1, -100, 100]],
            'integer array with max values' => [[2147483647, -2147483648, 0]],
        ];
    }

    #[DataProvider('provideInvalidItems')]
    #[Test]
    public function rejects_invalid_item(mixed $item): void
    {
        $this->expectException(InvalidIntegerArrayItemForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), [$item]);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidItems(): array
    {
        return [
            'above maximum' => [2147483648],
            'below minimum' => [-2147483649],
            'float value' => [1.5],
        ];
    }
}
