<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzRange;

/**
 * @extends RangeArrayTypeTestCase<TstzRange>
 */
final class TstzRangeArrayTypeTest extends RangeArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'tstzrange[]';
    }

    /**
     * @return class-string<TstzRange>
     */
    protected static function getRangeValueObjectClass(): string
    {
        return TstzRange::class;
    }

    /**
     * @return array<string, array{array<TstzRange|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single tstzrange' => [[new TstzRange(
                new \DateTimeImmutable('2023-01-01 10:00:00+00:00'),
                new \DateTimeImmutable('2023-01-01 18:00:00+00:00'),
                false,
                false
            )]],
            'multiple tstzranges' => [[
                new TstzRange(
                    new \DateTimeImmutable('2023-01-01 08:00:00+00:00'),
                    new \DateTimeImmutable('2023-01-01 12:00:00+00:00'),
                    false,
                    false
                ),
                new TstzRange(
                    new \DateTimeImmutable('2023-01-01 13:00:00+00:00'),
                    new \DateTimeImmutable('2023-01-01 17:00:00+00:00'),
                    false,
                    false
                ),
            ]],
            'array with null item' => [[new TstzRange(
                new \DateTimeImmutable('2023-01-01 10:00:00+00:00'),
                new \DateTimeImmutable('2023-01-01 18:00:00+00:00'),
                false,
                false
            ), null]],
        ];
    }
}
