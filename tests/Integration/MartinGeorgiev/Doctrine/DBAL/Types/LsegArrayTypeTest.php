<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Lseg as LsegValueObject;

class LsegArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'lseg[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'LSEG[]';
    }

    /**
     * @return array<string, array{array<int, LsegValueObject>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single segment' => [[
                LsegValueObject::fromString('[(0,0),(1,1)]'),
            ]],
            'multiple segments' => [[
                LsegValueObject::fromString('[(1.5,2.5),(3.5,4.5)]'),
                LsegValueObject::fromString('[(-1,-2),(-3,-4)]'),
            ]],
        ];
    }
}
