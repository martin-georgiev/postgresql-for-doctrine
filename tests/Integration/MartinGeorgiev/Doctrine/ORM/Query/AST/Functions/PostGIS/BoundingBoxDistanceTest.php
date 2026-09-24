<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\BoundingBoxDistance;
use PHPUnit\Framework\Attributes\Test;

final class BoundingBoxDistanceTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'BOUNDING_BOX_DISTANCE' => BoundingBoxDistance::class,
        ];
    }

    #[Test]
    public function returns_the_bounding_box_distance_from_a_literal_operand(): void
    {
        $dql = "SELECT BOUNDING_BOX_DISTANCE(g.geometry1, 'POLYGON((1 1, 3 1, 3 3, 1 3, 1 1))') as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.4142135623730951, $result[0]['distance']);
    }

    #[Test]
    public function returns_the_bounding_box_distance_from_entity_fields(): void
    {
        $dql = 'SELECT BOUNDING_BOX_DISTANCE(g.geometry1, g.geometry2) as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['distance']);
    }
}
