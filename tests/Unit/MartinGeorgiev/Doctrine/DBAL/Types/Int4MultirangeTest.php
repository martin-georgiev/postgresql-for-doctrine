<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Int4Multirange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Multirange as Int4MultirangeVO;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Range;

/**
 * @extends BaseMultirangeTypeTestCase<Int4MultirangeVO>
 */
class Int4MultirangeTest extends BaseMultirangeTypeTestCase
{
    protected function createMultirangeType(): Int4Multirange
    {
        return new Int4Multirange();
    }

    protected function getExpectedTypeName(): string
    {
        return 'int4multirange';
    }

    protected function getExpectedValueObjectClass(): string
    {
        return Int4MultirangeVO::class;
    }

    /**
     * @return array<string, array{Int4MultirangeVO, string}>
     */
    public static function provideValidDatabaseConversions(): array
    {
        return [
            'empty multirange' => [new Int4MultirangeVO([]), '{}'],
            'single range' => [new Int4MultirangeVO([new Int4Range(1, 10)]), '{[1,10)}'],
            'two ranges' => [
                new Int4MultirangeVO([new Int4Range(1, 5), new Int4Range(10, 20)]),
                '{[1,5),[10,20)}',
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
            'single range' => ['{[1,10)}', '{[1,10)}'],
            'two ranges' => ['{[1,5),[10,20)}', '{[1,5),[10,20)}'],
        ];
    }
}
