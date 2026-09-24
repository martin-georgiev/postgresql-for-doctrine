<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\NDimensionalCentroidDistance;
use PHPUnit\Framework\Attributes\Test;

final class NDimensionalCentroidDistanceTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ND_CENTROID_DISTANCE' => NDimensionalCentroidDistance::class,
        ];
    }

    #[Test]
    public function returns_the_centroid_distance_from_entity_fields(): void
    {
        $dql = 'SELECT ND_CENTROID_DISTANCE(g.geometry1, g.geometry2) as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsNumeric($result[0]['distance']);
        $this->assertGreaterThan(0, $result[0]['distance']);
    }

    #[Test]
    public function returns_the_centroid_distance_from_a_literal_operand(): void
    {
        $dql = "SELECT ND_CENTROID_DISTANCE(g.geometry1, 'POINT(3 3)') as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsNumeric($result[0]['distance']);
        $this->assertGreaterThan(0, $result[0]['distance']);
    }

    #[Test]
    public function returns_the_centroid_distance_from_wkt_literals(): void
    {
        $dql = "SELECT ND_CENTROID_DISTANCE('POINT(0 0)', 'POINT(1 1)') as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsNumeric($result[0]['distance']);
        $this->assertGreaterThan(0, $result[0]['distance']);
    }
}
