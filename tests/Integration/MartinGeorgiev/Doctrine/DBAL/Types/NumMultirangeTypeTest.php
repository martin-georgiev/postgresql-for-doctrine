<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericMultirange as NumericMultirangeVO;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;

class NumMultirangeTypeTest extends MultirangeTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'nummultirange';
    }

    protected function getPostgresTypeName(): string
    {
        return 'NUMMULTIRANGE';
    }

    /**
     * @return array<string, array{NumericMultirangeVO}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'empty multirange' => [new NumericMultirangeVO([])],
            'single decimal range' => [new NumericMultirangeVO([new NumericRange(1.5, 10.5)])],
            'two non-overlapping ranges' => [
                new NumericMultirangeVO([new NumericRange(1, 5), new NumericRange(10.5, 20.5)]),
            ],
        ];
    }
}
