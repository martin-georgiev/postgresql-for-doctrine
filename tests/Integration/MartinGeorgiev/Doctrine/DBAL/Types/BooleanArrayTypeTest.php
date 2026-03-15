<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

class BooleanArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'bool[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'BOOL[]';
    }

    /**
     * @return array<string, array{string, array<int, bool>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple boolean array' => ['simple boolean array', [true, false, true]],
            'boolean array with all true' => ['boolean array with all true', [true, true, true]],
            'boolean array with all false' => ['boolean array with all false', [false, false, false]],
            'boolean array mixed' => ['boolean array mixed', [true, false, true, false, true]],
        ];
    }
}
