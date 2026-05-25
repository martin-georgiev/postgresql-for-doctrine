<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateRange;

class DateRangeArrayTypeTest extends RangeArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'daterange[]';
    }

    /**
     * @return class-string<DateRange>
     */
    public static function getRangeValueObjectClass(): string
    {
        return DateRange::class;
    }

    /**
     * @return array<string, array{array<DateRange|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single daterange' => [[new DateRange(
                new \DateTimeImmutable('2023-01-01'),
                new \DateTimeImmutable('2023-12-31'),
                true,
                false
            )]],
            'multiple dateranges' => [[
                new DateRange(
                    new \DateTimeImmutable('2023-01-01'),
                    new \DateTimeImmutable('2023-06-30'),
                    true,
                    false
                ),
                new DateRange(
                    new \DateTimeImmutable('2023-07-01'),
                    new \DateTimeImmutable('2023-12-31'),
                    true,
                    false
                ),
            ]],
            'daterange via factory methods' => [[
                DateRange::year(2022),
                DateRange::month(2023, 3),
            ]],
            'array with null item' => [[new DateRange(
                new \DateTimeImmutable('2023-01-01'),
                new \DateTimeImmutable('2023-12-31'),
                true,
                false
            ), null]],
        ];
    }
}
