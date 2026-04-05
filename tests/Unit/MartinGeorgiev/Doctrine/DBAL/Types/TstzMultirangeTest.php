<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\TstzMultirange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzMultirange as TstzMultirangeVO;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzRange;

/**
 * @extends BaseMultirangeTypeTestCase<TstzMultirangeVO>
 */
class TstzMultirangeTest extends BaseMultirangeTypeTestCase
{
    protected function createMultirangeType(): TstzMultirange
    {
        return new TstzMultirange();
    }

    protected function getExpectedTypeName(): string
    {
        return 'tstzmultirange';
    }

    protected function getExpectedValueObjectClass(): string
    {
        return TstzMultirangeVO::class;
    }

    /**
     * @return array<string, array{TstzMultirangeVO, string}>
     */
    public static function provideValidDatabaseConversions(): array
    {
        return [
            'empty multirange' => [new TstzMultirangeVO([]), '{}'],
            'single range' => [
                new TstzMultirangeVO([new TstzRange(new \DateTimeImmutable('2024-01-01 09:00:00+00:00'), new \DateTimeImmutable('2024-01-01 17:00:00+00:00'))]),
                '{[2024-01-01 09:00:00.000000+00:00,2024-01-01 17:00:00.000000+00:00)}',
            ],
            'two ranges' => [
                new TstzMultirangeVO([
                    new TstzRange(new \DateTimeImmutable('2024-01-01 09:00:00+00:00'), new \DateTimeImmutable('2024-01-01 12:00:00+00:00')),
                    new TstzRange(new \DateTimeImmutable('2024-01-01 14:00:00+00:00'), new \DateTimeImmutable('2024-01-01 17:00:00+00:00')),
                ]),
                '{[2024-01-01 09:00:00.000000+00:00,2024-01-01 12:00:00.000000+00:00),[2024-01-01 14:00:00.000000+00:00,2024-01-01 17:00:00.000000+00:00)}',
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
            'single range' => ['{[2024-01-01 09:00:00+00:00,2024-01-01 17:00:00+00:00)}', '{[2024-01-01 09:00:00.000000+00:00,2024-01-01 17:00:00.000000+00:00)}'],
            'two ranges' => [
                '{[2024-01-01 09:00:00+00:00,2024-01-01 12:00:00+00:00),[2024-01-01 14:00:00+00:00,2024-01-01 17:00:00+00:00)}',
                '{[2024-01-01 09:00:00.000000+00:00,2024-01-01 12:00:00.000000+00:00),[2024-01-01 14:00:00.000000+00:00,2024-01-01 17:00:00.000000+00:00)}',
            ],
        ];
    }
}
