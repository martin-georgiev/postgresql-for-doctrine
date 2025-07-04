<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\DateRange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateRange as DateRangeValueObject;

final class DateRangeTest extends BaseRangeTestCase
{
    protected function createRangeType(): Type
    {
        return new DateRange();
    }

    protected function getExpectedTypeName(): string
    {
        return 'daterange';
    }

    protected function getExpectedSqlDeclaration(): string
    {
        return 'daterange';
    }

    protected function getExpectedValueObjectClass(): string
    {
        return DateRangeValueObject::class;
    }

    public static function provideValidTransformations(): \Generator
    {
        yield 'simple range' => [
            new DateRangeValueObject(new \DateTimeImmutable('2023-01-01'), new \DateTimeImmutable('2023-12-31')),
            '[2023-01-01,2023-12-31)',
        ];
        yield 'inclusive range' => [
            new DateRangeValueObject(new \DateTimeImmutable('2023-01-01'), new \DateTimeImmutable('2023-01-10'), true, true),
            '[2023-01-01,2023-01-10]',
        ];
        yield 'exclusive range' => [
            new DateRangeValueObject(new \DateTimeImmutable('2023-01-01'), new \DateTimeImmutable('2023-01-10'), false, false),
            '(2023-01-01,2023-01-10)',
        ];
        yield 'infinite lower' => [
            new DateRangeValueObject(null, new \DateTimeImmutable('2023-12-31')),
            '[,2023-12-31)',
        ];
        yield 'infinite upper' => [
            new DateRangeValueObject(new \DateTimeImmutable('2023-01-01'), null),
            '[2023-01-01,)',
        ];
        yield 'empty range' => [
            DateRangeValueObject::empty(),
            'empty',
        ];
        yield 'null value' => [
            null,
            null,
        ];
    }
}
