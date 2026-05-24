<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsMultirange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsRange;

class TsMultirangeArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'tsmultirange[]';
    }

    /**
     * @return array<string, array{array<int, TsMultirange|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single empty multirange' => [[new TsMultirange([])]],
            'single multirange with one range' => [
                [new TsMultirange([new TsRange(new \DateTimeImmutable('2024-01-01 09:00:00'), new \DateTimeImmutable('2024-01-01 17:00:00'))])],
            ],
            'array of two multiranges' => [
                [
                    new TsMultirange([new TsRange(new \DateTimeImmutable('2024-01-01 09:00:00'), new \DateTimeImmutable('2024-01-01 12:00:00'))]),
                    new TsMultirange([
                        new TsRange(new \DateTimeImmutable('2024-01-02 09:00:00'), new \DateTimeImmutable('2024-01-02 12:00:00')),
                        new TsRange(new \DateTimeImmutable('2024-01-02 14:00:00'), new \DateTimeImmutable('2024-01-02 17:00:00')),
                    ]),
                ],
            ],
            'array with null item' => [
                [new TsMultirange([new TsRange(new \DateTimeImmutable('2024-01-01 09:00:00'), new \DateTimeImmutable('2024-01-01 17:00:00'))]), null],
            ],
        ];
    }
}
