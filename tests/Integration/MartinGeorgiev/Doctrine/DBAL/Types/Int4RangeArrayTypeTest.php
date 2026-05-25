<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Range;

class Int4RangeArrayTypeTest extends RangeArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'int4range[]';
    }

    /**
     * @return class-string<Int4Range>
     */
    public static function getRangeValueObjectClass(): string
    {
        return Int4Range::class;
    }

    /**
     * @return array<string, array{array<Int4Range|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single int4range' => [[new Int4Range(1, 10, true, false)]],
            'multiple int4ranges' => [[
                new Int4Range(1, 5, true, false),
                new Int4Range(10, 20, true, false),
            ]],
            'int4range with negative values' => [[new Int4Range(-100, 100, true, false)]],
            'array with null item' => [[new Int4Range(1, 10, true, false), null]],
        ];
    }
}
