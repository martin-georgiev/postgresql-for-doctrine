<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Int8Range;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Range as Int8RangeValueObject;

final class Int8RangeTest extends BaseRangeTestCase
{
    protected function createRangeType(): Int8Range
    {
        return new Int8Range();
    }

    protected function getExpectedTypeName(): string
    {
        return 'int8range';
    }

    protected function getExpectedSqlDeclaration(): string
    {
        return 'int8range';
    }

    protected function getExpectedValueObjectClass(): string
    {
        return Int8RangeValueObject::class;
    }

    public static function provideValidTransformations(): \Generator
    {
        yield 'simple range' => [
            new Int8RangeValueObject(1, 1000),
            '[1,1000)',
        ];
        yield 'inclusive range' => [
            new Int8RangeValueObject(1, 10, true, true),
            '[1,10]',
        ];
        yield 'exclusive range' => [
            new Int8RangeValueObject(1, 10, false, false),
            '(1,10)',
        ];
        yield 'infinite lower' => [
            new Int8RangeValueObject(null, 100),
            '[,100)',
        ];
        yield 'infinite upper' => [
            new Int8RangeValueObject(1, null),
            '[1,)',
        ];
        yield 'max bounds' => [
            new Int8RangeValueObject(PHP_INT_MIN, PHP_INT_MAX),
            '['.PHP_INT_MIN.','.PHP_INT_MAX.')',
        ];
        yield 'empty range' => [
            Int8RangeValueObject::empty(),
            'empty',
        ];
        yield 'null value' => [
            null,
            null,
        ];
    }
}
