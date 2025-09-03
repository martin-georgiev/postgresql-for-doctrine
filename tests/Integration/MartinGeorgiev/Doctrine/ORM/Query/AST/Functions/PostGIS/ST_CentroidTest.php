<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Centroid;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Contains;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Distance;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use PHPUnit\Framework\Attributes\Test;

class ST_CentroidTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_CENTROID' => ST_Centroid::class,
            'ST_CONTAINS' => ST_Contains::class,
            'ST_DISTANCE' => ST_Distance::class,
            'ST_EQUALS' => ST_Equals::class,
        ];
    }

    #[Test]
    public function returns_centroid_for_point(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_CENTROID(g.geometry1), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_centroid_for_polygon(): void
    {
        $dql = 'SELECT ST_CONTAINS(g.geometry1, ST_CENTROID(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_centroid_for_linestring(): void
    {
        $dql = 'SELECT ST_DISTANCE(ST_CENTROID(g.geometry1), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result'], 'should return point on linestring (distance = 0)');
    }

    #[Test]
    public function returns_centroid_for_overlapping_polygons(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_CENTROID(g.geometry1), ST_CENTROID(g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'both polygons shall have the same centroid');
    }
}
