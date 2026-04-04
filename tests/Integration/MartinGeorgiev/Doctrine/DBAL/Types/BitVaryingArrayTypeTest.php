<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBitVaryingArrayItemForDatabaseException;
use PHPUnit\Framework\Attributes\Test;

class BitVaryingArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'bit varying[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'BIT VARYING[]';
    }

    /**
     * @return array<string, array{string, array<int, string|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single zero' => ['single zero', ['0']],
            'single one' => ['single one', ['1']],
            'varying length bits' => ['varying length bits', ['1', '10', '101']],
            'all zeros' => ['all zeros', ['00000000']],
            'all ones' => ['all ones', ['11111111']],
            'array with null item' => ['array with null item', ['101', null, '010']],
            'empty array' => ['empty array', []],
        ];
    }

    #[Test]
    public function rejects_invalid_bit_string(): void
    {
        $this->expectException(InvalidBitVaryingArrayItemForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), ['abc']);
    }

    #[Test]
    public function rejects_non_bit_digit(): void
    {
        $this->expectException(InvalidBitVaryingArrayItemForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), ['2']);
    }
}
