<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzMultirange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzRange;

class TstzMultirangeArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'tstzmultirange[]';
    }

    /**
     * @return array<string, array{array<int, TstzMultirange|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single empty multirange' => [[new TstzMultirange([])]],
            'single multirange with one range' => [
                [new TstzMultirange([new TstzRange(new \DateTimeImmutable('2024-01-01 09:00:00+00:00'), new \DateTimeImmutable('2024-01-01 17:00:00+00:00'))])],
            ],
            'array of two multiranges' => [
                [
                    new TstzMultirange([new TstzRange(new \DateTimeImmutable('2024-01-01 09:00:00+00:00'), new \DateTimeImmutable('2024-01-01 12:00:00+00:00'))]),
                    new TstzMultirange([
                        new TstzRange(new \DateTimeImmutable('2024-01-02 09:00:00+00:00'), new \DateTimeImmutable('2024-01-02 12:00:00+00:00')),
                        new TstzRange(new \DateTimeImmutable('2024-01-02 14:00:00+00:00'), new \DateTimeImmutable('2024-01-02 17:00:00+00:00')),
                    ]),
                ],
            ],
            'array with null item' => [
                [new TstzMultirange([new TstzRange(new \DateTimeImmutable('2024-01-01 09:00:00+00:00'), new \DateTimeImmutable('2024-01-01 17:00:00+00:00'))]), null],
            ],
        ];
    }
}
