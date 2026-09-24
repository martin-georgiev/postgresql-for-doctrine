<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\GeometryDistance;
use PHPUnit\Framework\Attributes\Test;

final class GeometryDistanceTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'GEOMETRY_DISTANCE' => GeometryDistance::class,
        ];
    }

    #[Test]
    public function returns_the_euclidean_distance_from_entity_fields(): void
    {
        $dql = 'SELECT GEOMETRY_DISTANCE(g.geometry1, g.geometry2) as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.4142135623730951, $result[0]['distance']);
    }

    #[Test]
    public function returns_the_euclidean_distance_from_a_literal_operand(): void
    {
        $dql = "SELECT GEOMETRY_DISTANCE(g.geometry1, 'SRID=4326;POINT(1 1)') as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.4142135623730951, $result[0]['distance']);
    }
}
