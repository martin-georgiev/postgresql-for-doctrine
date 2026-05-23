<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidFloatArrayItemForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class RealArrayTypeTest extends ArrayTypeTestCase
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

    #[DataProvider('provideInvalidItems')]
    #[Test]
    public function rejects_invalid_item(mixed $item): void
    {
        $this->expectException(InvalidFloatArrayItemForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), [$item]);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidItems(): array
    {
        return [
            'non-numeric boolean' => [true],
            'excess decimal precision' => ['1.1234567'],
        ];
    }
}
