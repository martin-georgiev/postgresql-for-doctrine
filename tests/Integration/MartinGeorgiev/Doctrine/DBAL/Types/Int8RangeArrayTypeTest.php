<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Range;

/**
 * @extends RangeArrayTypeTestCase<Int8Range>
 */
class Int8RangeArrayTypeTest extends RangeArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'int8range[]';
    }

    /**
     * @return class-string<Int8Range>
     */
    protected static function getRangeValueObjectClass(): string
    {
        return Int8Range::class;
    }

    /**
     * @return array<string, array{array<Int8Range|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single int8range' => [[new Int8Range(1, 100, true, false)]],
            'multiple int8ranges' => [[
                new Int8Range(1, 50, true, false),
                new Int8Range(100, 200, true, false),
            ]],
            'int8range with large values' => [[new Int8Range(1000000000, 9999999999, true, false)]],
            'array with null item' => [[new Int8Range(1, 100, true, false), null]],
        ];
    }
}
