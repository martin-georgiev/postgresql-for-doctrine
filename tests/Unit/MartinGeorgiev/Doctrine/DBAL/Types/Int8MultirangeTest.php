<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Int8Multirange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Multirange as Int8MultirangeVO;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Range;

/**
 * @extends BaseMultirangeTypeTestCase<Int8MultirangeVO>
 */
class Int8MultirangeTest extends BaseMultirangeTypeTestCase
{
    protected function createMultirangeType(): Int8Multirange
    {
        return new Int8Multirange();
    }

    protected function getExpectedTypeName(): string
    {
        return 'int8multirange';
    }

    protected function getExpectedValueObjectClass(): string
    {
        return Int8MultirangeVO::class;
    }

    /**
     * @return array<string, array{Int8MultirangeVO, string}>
     */
    public static function provideValidDatabaseConversions(): array
    {
        return [
            'empty multirange' => [new Int8MultirangeVO([]), '{}'],
            'single range' => [new Int8MultirangeVO([new Int8Range(1, 1000000000)]), '{[1,1000000000)}'],
            'two ranges' => [
                new Int8MultirangeVO([new Int8Range(1, 1000), new Int8Range(2000, 3000)]),
                '{[1,1000),[2000,3000)}',
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
            'single range' => ['{[1,1000000000)}', '{[1,1000000000)}'],
            'two ranges' => ['{[1,1000),[2000,3000)}', '{[1,1000),[2000,3000)}'],
        ];
    }
}
