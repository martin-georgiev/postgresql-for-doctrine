<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Disjoint;
use PHPUnit\Framework\Attributes\Test;

class ST_DisjointTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_DISJOINT' => ST_Disjoint::class,
        ];
    }

    #[Test]
    public function returns_true_when_comparing_separate_point_geometries(): void
    {
        $dql = 'SELECT ST_DISJOINT(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_comparing_identical_geometries(): void
    {
        $dql = 'SELECT ST_DISJOINT(g.geometry1, g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_polygons_overlap(): void
    {
        $dql = 'SELECT ST_DISJOINT(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
