<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

final class BooleanArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'bool[]';
    }

    /**
     * @return array<string, array{array<int, bool|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple boolean array' => [[true, false, true]],
            'boolean array with all true' => [[true, true, true]],
            'boolean array with all false' => [[false, false, false]],
            'boolean array mixed' => [[true, false, true, false, true]],
            'boolean array with a null element' => [[true, null, false]],
            'boolean array of only null elements' => [[null, null]],
            'empty boolean array' => [[]],
        ];
    }
}
