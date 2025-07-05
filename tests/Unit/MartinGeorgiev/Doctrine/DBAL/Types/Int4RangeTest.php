<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Int4Range;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Range as Int4RangeValueObject;

final class Int4RangeTest extends BaseRangeTestCase
{
    protected function createRangeType(): Int4Range
    {
        return new Int4Range();
    }

    protected function getExpectedTypeName(): string
    {
        return 'int4range';
    }

    protected function getExpectedSqlDeclaration(): string
    {
        return 'int4range';
    }

    protected function getExpectedValueObjectClass(): string
    {
        return Int4RangeValueObject::class;
    }

    public static function provideValidTransformations(): \Generator
    {
        yield 'simple range' => [
            new Int4RangeValueObject(1, 1000),
            '[1,1000)',
        ];
        yield 'inclusive range' => [
            new Int4RangeValueObject(1, 10, true, true),
            '[1,10]',
        ];
        yield 'exclusive range' => [
            new Int4RangeValueObject(1, 10, false, false),
            '(1,10)',
        ];
        yield 'infinite lower' => [
            new Int4RangeValueObject(null, 100),
            '[,100)',
        ];
        yield 'infinite upper' => [
            new Int4RangeValueObject(1, null),
            '[1,)',
        ];
        yield 'max bounds' => [
            new Int4RangeValueObject(-2147483648, 2147483647),
            '[-2147483648,2147483647)',
        ];
        yield 'empty range' => [
            Int4RangeValueObject::empty(),
            'empty',
        ];
        yield 'null value' => [
            null,
            null,
        ];
    }
}
