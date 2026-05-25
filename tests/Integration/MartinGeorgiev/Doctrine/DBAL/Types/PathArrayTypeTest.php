<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPathArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Path as PathValueObject;
use PHPUnit\Framework\Attributes\Test;

final class PathArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'path[]';
    }

    /**
     * @return array<string, array{array<int, PathValueObject>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single open path' => [[
                PathValueObject::fromString('[(0,0),(1,1),(2,0)]'),
            ]],
            'open and closed paths' => [[
                PathValueObject::fromString('[(1.5,2.5),(3.5,4.5)]'),
                PathValueObject::fromString('((0,0),(1,1),(2,0))'),
            ]],
            'empty path array' => [[]],
        ];
    }

    #[Test]
    public function rejects_string_instead_of_value_object(): void
    {
        $this->expectException(InvalidPathArrayItemForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, ['[(0,0),(1,1),(2,0)]']);
    }
}
