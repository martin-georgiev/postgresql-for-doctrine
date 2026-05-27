<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsRange;

/**
 * @extends RangeArrayTypeTestCase<TsRange>
 */
final class TsRangeArrayTypeTest extends RangeArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'tsrange[]';
    }

    /**
     * @return class-string<TsRange>
     */
    protected static function getRangeValueObjectClass(): string
    {
        return TsRange::class;
    }

    /**
     * @return array<string, array{array<TsRange|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single tsrange' => [[new TsRange(
                new \DateTimeImmutable('2023-01-01 10:00:00'),
                new \DateTimeImmutable('2023-01-01 18:00:00'),
                false,
                false
            )]],
            'multiple tsranges' => [[
                new TsRange(
                    new \DateTimeImmutable('2023-01-01 08:00:00'),
                    new \DateTimeImmutable('2023-01-01 12:00:00'),
                    false,
                    false
                ),
                new TsRange(
                    new \DateTimeImmutable('2023-01-01 13:00:00'),
                    new \DateTimeImmutable('2023-01-01 17:00:00'),
                    false,
                    false
                ),
            ]],
            'array with null item' => [[new TsRange(
                new \DateTimeImmutable('2023-01-01 10:00:00'),
                new \DateTimeImmutable('2023-01-01 18:00:00'),
                false,
                false
            ), null]],
        ];
    }
}
