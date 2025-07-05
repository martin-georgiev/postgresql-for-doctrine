<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\TstzRange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzRange as TstzRangeValueObject;

final class TstzRangeTest extends BaseRangeTestCase
{
    protected function createRangeType(): TstzRange
    {
        return new TstzRange();
    }

    protected function getExpectedTypeName(): string
    {
        return 'tstzrange';
    }

    protected function getExpectedSqlDeclaration(): string
    {
        return 'tstzrange';
    }

    protected function getExpectedValueObjectClass(): string
    {
        return TstzRangeValueObject::class;
    }

    public static function provideValidTransformations(): \Generator
    {
        yield 'simple range' => [
            new TstzRangeValueObject(new \DateTimeImmutable('2023-01-01 10:00:00+00:00'), new \DateTimeImmutable('2023-01-01 18:00:00+00:00')),
            '[2023-01-01 10:00:00.000000+00:00,2023-01-01 18:00:00.000000+00:00)',
        ];
        yield 'inclusive range' => [
            new TstzRangeValueObject(new \DateTimeImmutable('2023-01-01 10:00:00+00:00'), new \DateTimeImmutable('2023-01-01 18:00:00+00:00'), true, true),
            '[2023-01-01 10:00:00.000000+00:00,2023-01-01 18:00:00.000000+00:00]',
        ];
        yield 'exclusive range' => [
            new TstzRangeValueObject(new \DateTimeImmutable('2023-01-01 10:00:00+00:00'), new \DateTimeImmutable('2023-01-01 18:00:00+00:00'), false, false),
            '(2023-01-01 10:00:00.000000+00:00,2023-01-01 18:00:00.000000+00:00)',
        ];
        yield 'infinite lower' => [
            new TstzRangeValueObject(null, new \DateTimeImmutable('2023-01-01 18:00:00+00:00')),
            '[,2023-01-01 18:00:00.000000+00:00)',
        ];
        yield 'infinite upper' => [
            new TstzRangeValueObject(new \DateTimeImmutable('2023-01-01 10:00:00+00:00'), null),
            '[2023-01-01 10:00:00.000000+00:00,)',
        ];
        yield 'empty range' => [
            TstzRangeValueObject::empty(),
            'empty',
        ];
        yield 'null value' => [
            null,
            null,
        ];
    }
}
