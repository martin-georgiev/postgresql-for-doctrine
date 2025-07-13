<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\TsRange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsRange as TsRangeValueObject;

/**
 * @extends BaseRangeTestCase<TsRangeValueObject>
 */
final class TsRangeTest extends BaseRangeTestCase
{
    protected function createRangeType(): TsRange
    {
        return new TsRange();
    }

    protected function getExpectedTypeName(): string
    {
        return 'tsrange';
    }

    protected function getExpectedValueObjectClass(): string
    {
        return TsRangeValueObject::class;
    }

    public static function provideValidTransformations(): \Generator
    {
        yield 'simple range' => [
            new TsRangeValueObject(new \DateTimeImmutable('2023-01-01 10:00:00'), new \DateTimeImmutable('2023-01-01 18:00:00')),
            '[2023-01-01 10:00:00.000000,2023-01-01 18:00:00.000000)',
        ];
        yield 'inclusive range' => [
            new TsRangeValueObject(new \DateTimeImmutable('2023-01-01 10:00:00'), new \DateTimeImmutable('2023-01-01 18:00:00'), true, true),
            '[2023-01-01 10:00:00.000000,2023-01-01 18:00:00.000000]',
        ];
        yield 'exclusive range' => [
            new TsRangeValueObject(new \DateTimeImmutable('2023-01-01 10:00:00'), new \DateTimeImmutable('2023-01-01 18:00:00'), false, false),
            '(2023-01-01 10:00:00.000000,2023-01-01 18:00:00.000000)',
        ];
        yield 'infinite lower' => [
            new TsRangeValueObject(null, new \DateTimeImmutable('2023-01-01 18:00:00')),
            '[,2023-01-01 18:00:00.000000)',
        ];
        yield 'infinite upper' => [
            new TsRangeValueObject(new \DateTimeImmutable('2023-01-01 10:00:00'), null),
            '[2023-01-01 10:00:00.000000,)',
        ];
        yield 'empty range' => [
            TsRangeValueObject::empty(),
            'empty',
        ];
        yield 'null value' => [
            null,
            null,
        ];
    }
}
