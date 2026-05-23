<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidIntegerArrayItemForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class SmallIntArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'smallint[]';
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
            'above maximum' => [32768],
            'below minimum' => [-32769],
            'float value' => [1.5],
        ];
    }
}
