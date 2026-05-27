<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPolygonForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Polygon as PolygonValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class PolygonTypeTest extends TestCase
{
    protected function getTypeName(): string
    {
        return 'polygon';
    }

    #[Test]
    public function roundtrips_null_value(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, null);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function roundtrips_value(PolygonValueObject $polygonValueObject): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $polygonValueObject);
    }

    /**
     * @return array<string, array{PolygonValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'triangle' => [PolygonValueObject::fromString('((0,0),(1,1),(2,0))')],
            'square' => [PolygonValueObject::fromString('((0,0),(0,1),(1,1),(1,0))')],
            'polygon with floats' => [PolygonValueObject::fromString('((1.5,2.5),(3.5,4.5),(5.5,6.5))')],
            'polygon with negative coordinates' => [PolygonValueObject::fromString('((-1,-2),(-3,-4),(-5,-6))')],
        ];
    }

    #[Test]
    public function rejects_string_instead_of_value_object(): void
    {
        $this->expectException(InvalidPolygonForPHPException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, '((0,0),(1,1),(2,0))');
    }
}
