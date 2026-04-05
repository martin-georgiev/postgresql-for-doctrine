<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\TsMultirange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsMultirange as TsMultirangeVO;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsRange;

/**
 * @extends BaseMultirangeTypeTestCase<TsMultirangeVO>
 */
class TsMultirangeTest extends BaseMultirangeTypeTestCase
{
    protected function createMultirangeType(): TsMultirange
    {
        return new TsMultirange();
    }

    protected function getExpectedTypeName(): string
    {
        return 'tsmultirange';
    }

    protected function getExpectedValueObjectClass(): string
    {
        return TsMultirangeVO::class;
    }

    /**
     * @return array<string, array{TsMultirangeVO, string}>
     */
    public static function provideValidDatabaseConversions(): array
    {
        return [
            'empty multirange' => [new TsMultirangeVO([]), '{}'],
            'single range' => [
                new TsMultirangeVO([new TsRange(new \DateTimeImmutable('2024-01-01 09:00:00'), new \DateTimeImmutable('2024-01-01 17:00:00'))]),
                '{[2024-01-01 09:00:00.000000,2024-01-01 17:00:00.000000)}',
            ],
            'two ranges' => [
                new TsMultirangeVO([
                    new TsRange(new \DateTimeImmutable('2024-01-01 09:00:00'), new \DateTimeImmutable('2024-01-01 12:00:00')),
                    new TsRange(new \DateTimeImmutable('2024-01-01 14:00:00'), new \DateTimeImmutable('2024-01-01 17:00:00')),
                ]),
                '{[2024-01-01 09:00:00.000000,2024-01-01 12:00:00.000000),[2024-01-01 14:00:00.000000,2024-01-01 17:00:00.000000)}',
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
            'single range' => ['{[2024-01-01 09:00:00,2024-01-01 17:00:00)}', '{[2024-01-01 09:00:00.000000,2024-01-01 17:00:00.000000)}'],
            'two ranges' => [
                '{[2024-01-01 09:00:00,2024-01-01 12:00:00),[2024-01-01 14:00:00,2024-01-01 17:00:00)}',
                '{[2024-01-01 09:00:00.000000,2024-01-01 12:00:00.000000),[2024-01-01 14:00:00.000000,2024-01-01 17:00:00.000000)}',
            ],
        ];
    }
}
