<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzMultirange as TstzMultirangeVO;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzRange;

final class TstzMultirangeTypeTest extends MultirangeTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'tstzmultirange';
    }

    /**
     * @return array<string, array{TstzMultirangeVO}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'empty multirange' => [new TstzMultirangeVO([])],
            'single timestamptz range' => [
                new TstzMultirangeVO([new TstzRange(new \DateTimeImmutable('2024-01-01 09:00:00+00:00'), new \DateTimeImmutable('2024-01-01 17:00:00+00:00'))]),
            ],
            'two non-overlapping timestamptz ranges' => [
                new TstzMultirangeVO([
                    new TstzRange(new \DateTimeImmutable('2024-01-01 09:00:00+00:00'), new \DateTimeImmutable('2024-01-01 12:00:00+00:00')),
                    new TstzRange(new \DateTimeImmutable('2024-01-01 14:00:00+00:00'), new \DateTimeImmutable('2024-01-01 17:00:00+00:00')),
                ]),
            ],
        ];
    }
}
