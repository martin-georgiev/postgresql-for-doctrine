<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;

/**
 * @extends RangeArrayTypeTestCase<NumericRange>
 */
final class NumRangeArrayTypeTest extends RangeArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'numrange[]';
    }

    /**
     * @return class-string<NumericRange>
     */
    protected static function getRangeValueObjectClass(): string
    {
        return NumericRange::class;
    }

    /**
     * @return array<string, array{array<NumericRange|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single numrange' => [[new NumericRange(1.5, 10.7, true, false)]],
            'multiple numranges' => [[
                new NumericRange(1.5, 5.5, true, false),
                new NumericRange(10.0, 20.0, true, false),
            ]],
            'numrange with integer values' => [[new NumericRange(1, 100, true, false)]],
            'array with null item' => [[new NumericRange(1.5, 10.7, true, false), null]],
        ];
    }
}
