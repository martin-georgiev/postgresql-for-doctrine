<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Box as BoxValueObject;

class BoxArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'box[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'BOX[]';
    }

    /**
     * @return array<string, array{array<int, BoxValueObject>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single box' => [[
                BoxValueObject::fromString('(3,4),(1,2)'),
            ]],
            'multiple boxes' => [[
                BoxValueObject::fromString('(1,1),(0,0)'),
                BoxValueObject::fromString('(-1,-2),(-3,-4)'),
            ]],
        ];
    }
}
