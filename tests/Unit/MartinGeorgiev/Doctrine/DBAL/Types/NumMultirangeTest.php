<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\NumMultirange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericMultirange as NumericMultirangeVO;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;

/**
 * @extends BaseMultirangeTypeTestCase<NumericMultirangeVO>
 */
class NumMultirangeTest extends BaseMultirangeTypeTestCase
{
    protected function createMultirangeType(): NumMultirange
    {
        return new NumMultirange();
    }

    protected function getExpectedTypeName(): string
    {
        return 'nummultirange';
    }

    protected function getExpectedValueObjectClass(): string
    {
        return NumericMultirangeVO::class;
    }

    /**
     * @return array<string, array{NumericMultirangeVO, string}>
     */
    public static function provideValidDatabaseConversions(): array
    {
        return [
            'empty multirange' => [new NumericMultirangeVO([]), '{}'],
            'single decimal range' => [new NumericMultirangeVO([new NumericRange(1.5, 10.5)]), '{[1.5,10.5)}'],
            'two ranges' => [
                new NumericMultirangeVO([new NumericRange(1, 5), new NumericRange(10.5, 20.5)]),
                '{[1,5),[10.5,20.5)}',
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
            'single decimal range' => ['{[1.5,10.5)}', '{[1.5,10.5)}'],
            'two ranges' => ['{[1,5),[10.5,20.5)}', '{[1,5),[10.5,20.5)}'],
        ];
    }
}
