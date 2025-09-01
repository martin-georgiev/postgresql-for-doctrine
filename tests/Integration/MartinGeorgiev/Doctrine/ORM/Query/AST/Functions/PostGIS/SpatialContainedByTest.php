<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\SpatialContainedBy;
use PHPUnit\Framework\Attributes\Test;

class SpatialContainedByTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SPATIAL_CONTAINED_BY' => SpatialContainedBy::class,
        ];
    }

    #[Test]
    public function returns_false_when_geometry_is_not_contained_by_another(): void
    {
        $dql = 'SELECT SPATIAL_CONTAINED_BY(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_geometry_is_contained_by_another(): void
    {
        $dql = 'SELECT SPATIAL_CONTAINED_BY(g.geometry2, g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_geometries_are_identical(): void
    {
        $dql = 'SELECT SPATIAL_CONTAINED_BY(g.geometry1, g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
