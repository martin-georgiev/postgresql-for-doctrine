<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Circle as CircleValueObject;

class CircleArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'circle[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'CIRCLE[]';
    }

    /**
     * @return array<string, array{string, array<int, CircleValueObject>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single circle' => ['single circle', [
                CircleValueObject::fromString('<(0,0),1>'),
            ]],
            'multiple circles' => ['multiple circles', [
                CircleValueObject::fromString('<(1.5,2.5),3.5>'),
                CircleValueObject::fromString('<(-10,-20),5>'),
            ]],
        ];
    }
}
