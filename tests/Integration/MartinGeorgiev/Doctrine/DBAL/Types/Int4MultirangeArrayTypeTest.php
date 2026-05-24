<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Multirange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Range;

class Int4MultirangeArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'int4multirange[]';
    }

    /**
     * @return array<string, array{array<int, Int4Multirange|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single empty multirange' => [[new Int4Multirange([])]],
            'single multirange with one range' => [[new Int4Multirange([new Int4Range(1, 10)])]],
            'array of two multiranges' => [
                [
                    new Int4Multirange([new Int4Range(1, 5)]),
                    new Int4Multirange([new Int4Range(10, 20), new Int4Range(30, 40)]),
                ],
            ],
            'array with null item' => [[new Int4Multirange([new Int4Range(1, 5)]), null]],
        ];
    }
}
