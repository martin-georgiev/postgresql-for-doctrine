<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateMultirange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateRange;

class DateMultirangeArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'datemultirange[]';
    }

    /**
     * @return array<string, array{array<int, DateMultirange|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single empty multirange' => [[new DateMultirange([])]],
            'single multirange with one range' => [
                [new DateMultirange([new DateRange(new \DateTimeImmutable('2024-01-01'), new \DateTimeImmutable('2024-06-30'))])],
            ],
            'array of two multiranges' => [
                [
                    new DateMultirange([new DateRange(new \DateTimeImmutable('2024-01-01'), new \DateTimeImmutable('2024-03-31'))]),
                    new DateMultirange([
                        new DateRange(new \DateTimeImmutable('2024-04-01'), new \DateTimeImmutable('2024-06-30')),
                        new DateRange(new \DateTimeImmutable('2024-10-01'), new \DateTimeImmutable('2024-12-31')),
                    ]),
                ],
            ],
            'array with null item' => [
                [new DateMultirange([new DateRange(new \DateTimeImmutable('2024-01-01'), new \DateTimeImmutable('2024-06-30'))]), null],
            ],
        ];
    }
}
