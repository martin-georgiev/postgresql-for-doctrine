<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\DateMultirange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateMultirange as DateMultirangeVO;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateRange;

/**
 * @extends BaseMultirangeTypeTestCase<DateMultirangeVO>
 */
class DateMultirangeTest extends BaseMultirangeTypeTestCase
{
    protected function createMultirangeType(): DateMultirange
    {
        return new DateMultirange();
    }

    protected function getExpectedTypeName(): string
    {
        return 'datemultirange';
    }

    protected function getExpectedValueObjectClass(): string
    {
        return DateMultirangeVO::class;
    }

    /**
     * @return array<string, array{DateMultirangeVO, string}>
     */
    public static function provideValidDatabaseConversions(): array
    {
        return [
            'empty multirange' => [new DateMultirangeVO([]), '{}'],
            'single range' => [
                new DateMultirangeVO([new DateRange(new \DateTimeImmutable('2024-01-01'), new \DateTimeImmutable('2024-06-30'))]),
                '{[2024-01-01,2024-06-30)}',
            ],
            'two ranges' => [
                new DateMultirangeVO([
                    new DateRange(new \DateTimeImmutable('2024-01-01'), new \DateTimeImmutable('2024-03-31')),
                    new DateRange(new \DateTimeImmutable('2024-07-01'), new \DateTimeImmutable('2024-12-31')),
                ]),
                '{[2024-01-01,2024-03-31),[2024-07-01,2024-12-31)}',
            ],
        ];
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideValidPHPConversions(): array
    {
        return [
            'empty multirange' => ['{}', '{}'],
            'single range' => ['{[2024-01-01,2024-06-30)}', '{[2024-01-01,2024-06-30)}'],
            'two ranges' => ['{[2024-01-01,2024-03-31),[2024-07-01,2024-12-31)}', '{[2024-01-01,2024-03-31),[2024-07-01,2024-12-31)}'],
        ];
    }
}
