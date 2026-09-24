<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\SpatialContainedBy;
use PHPUnit\Framework\Attributes\Test;

final class SpatialContainedByTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SPATIAL_CONTAINED_BY' => SpatialContainedBy::class,
        ];
    }

    #[Test]
    public function returns_false_when_first_polygon_is_not_contained_by_overlapping_second(): void
    {
        $dql = 'SELECT SPATIAL_CONTAINED_BY(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_first_polygon_is_not_contained_by_overlapping_second_from_a_literal_operand(): void
    {
        $dql = "SELECT SPATIAL_CONTAINED_BY(g.geometry1, 'SRID=4326;POLYGON((1 1, 1 3, 3 3, 3 1, 1 1))') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2";

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
