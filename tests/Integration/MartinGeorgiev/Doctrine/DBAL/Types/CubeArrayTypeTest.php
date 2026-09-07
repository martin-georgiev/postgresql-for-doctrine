<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCubeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Cube as CubeValueObject;
use PHPUnit\Framework\Attributes\Test;

final class CubeArrayTypeTest extends ArrayTypeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->ensurePostgresExtensionInSchema('cube');
    }

    protected function getTypeName(): string
    {
        return 'cube[]';
    }

    /**
     * @return array<string, array{array<int, CubeValueObject>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single point' => [[
                CubeValueObject::point(1.0, 2.0),
            ]],
            'multiple boxes' => [[
                new CubeValueObject([1.0, 2.0], [3.0, 4.0]),
                new CubeValueObject([-1.5, -2.5], [-0.5, -0.25]),
            ]],
            // PostgreSQL leaves a one-dimensional cube unquoted in the array
            // output because it contains no comma, unlike its neighbours.
            'one-dimensional points mixed with multi-dimensional ones' => [[
                CubeValueObject::point(1.0),
                CubeValueObject::point(2.0, 3.0),
                CubeValueObject::point(4.0),
            ]],
        ];
    }

    #[Test]
    public function rejects_string_instead_of_value_object(): void
    {
        $this->expectException(InvalidCubeArrayItemForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, ['(1, 2)']);
    }
}
