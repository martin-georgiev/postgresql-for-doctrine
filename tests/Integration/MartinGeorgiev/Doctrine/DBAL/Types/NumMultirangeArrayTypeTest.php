<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericMultirange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;

final class NumMultirangeArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'nummultirange[]';
    }

    /**
     * @return array<string, array{array<int, NumericMultirange|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single empty multirange' => [[new NumericMultirange([])]],
            'single multirange with one range' => [[new NumericMultirange([new NumericRange(1.5, 10.5)])]],
            'array of two multiranges' => [
                [
                    new NumericMultirange([new NumericRange(1, 5)]),
                    new NumericMultirange([new NumericRange(10.5, 20.5), new NumericRange(30.0, 40.0)]),
                ],
            ],
            'array with null item' => [[new NumericMultirange([new NumericRange(1.5, 10.5)]), null]],
        ];
    }
}
