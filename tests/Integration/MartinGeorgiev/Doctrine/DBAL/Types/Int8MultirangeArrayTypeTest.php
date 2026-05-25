<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Multirange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Range;

final class Int8MultirangeArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'int8multirange[]';
    }

    /**
     * @return array<string, array{array<int, Int8Multirange|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single empty multirange' => [[new Int8Multirange([])]],
            'single multirange with one range' => [[new Int8Multirange([new Int8Range(1, 1000000000)])]],
            'array of two multiranges' => [
                [
                    new Int8Multirange([new Int8Range(1, 1000)]),
                    new Int8Multirange([new Int8Range(2000, 3000), new Int8Range(5000, 6000)]),
                ],
            ],
            'multirange with int64-only values' => [
                [new Int8Multirange([new Int8Range(2147483648, 9223372036854775807)])],
            ],
        ];
    }
}
