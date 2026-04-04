<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Path as PathValueObject;

class PathArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'path[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'PATH[]';
    }

    /**
     * @return array<string, array{string, array<int, PathValueObject>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single open path' => ['single open path', [
                PathValueObject::fromString('[(0,0),(1,1),(2,0)]'),
            ]],
            'open and closed paths' => ['open and closed paths', [
                PathValueObject::fromString('[(1.5,2.5),(3.5,4.5)]'),
                PathValueObject::fromString('((0,0),(1,1),(2,0))'),
            ]],
        ];
    }
}
