<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\NumRange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;

/**
 * @extends BaseRangeTestCase<NumericRange>
 */
final class NumRangeTest extends BaseRangeTestCase
{
    protected function createRangeType(): NumRange
    {
        return new NumRange();
    }

    protected function getExpectedTypeName(): string
    {
        return 'numrange';
    }

    protected function getExpectedValueObjectClass(): string
    {
        return NumericRange::class;
    }

    public static function provideValidTransformations(): \Generator
    {
        yield 'simple range' => [
            new NumericRange(1.5, 10.7),
            '[1.5,10.7)',
        ];
        yield 'inclusive range' => [
            new NumericRange(1, 10, true, true),
            '[1,10]',
        ];
        yield 'exclusive range' => [
            new NumericRange(1, 10, false, false),
            '(1,10)',
        ];
        yield 'infinite lower' => [
            new NumericRange(null, 100),
            '[,100)',
        ];
        yield 'infinite upper' => [
            new NumericRange(1, null),
            '[1,)',
        ];
        yield 'empty range' => [
            NumericRange::empty(),
            'empty',
        ];
        yield 'null value' => [
            null,
            null,
        ];
    }
}
