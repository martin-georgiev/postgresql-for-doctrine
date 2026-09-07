<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

final class VarcharArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'varchar[]';
    }

    public static function provideValidTransformations(): array
    {
        return [
            'simple varchar array' => [['foo', 'bar', 'baz']],
            'varchar array with special chars' => [['foo"bar', 'baz\qux', 'with,comma']],
            'varchar array with empty strings' => [['', 'not empty', '']],
            'varchar array with unicode' => [['café', 'naïve', 'résumé']],
            'varchar array with numbers as strings' => [['123', '456', '789']],
            'varchar array with decimal strings with trailing zeros' => [['502.00', '505.00', '1.0']],
        ];
    }
}
