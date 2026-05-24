<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

class ByteaArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'bytea[]';
    }

    /**
     * @return array<string, array{array<int, string|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'array of ascii strings' => [['hello', 'world']],
            'array with null item' => [['hello', null, 'world']],
            'array with binary data' => [["binary\x00data", "\xFF\xFE"]],
            'array with empty string' => [['']],
        ];
    }
}
