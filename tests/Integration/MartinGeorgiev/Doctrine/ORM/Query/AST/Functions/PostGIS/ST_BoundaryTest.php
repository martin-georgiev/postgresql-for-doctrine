<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Boundary;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use PHPUnit\Framework\Attributes\Test;

class ST_BoundaryTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_BOUNDARY' => ST_Boundary::class,
            'ST_LENGTH' => ST_Length::class,
        ];
    }

    #[Test]
    public function returns_boundary_for_polygon(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_BOUNDARY(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result'], 'boundary of POLYGON((0 0, 0 4, 4 4, 4 0, 0 0)) should be a LineString with perimeter = 16');
    }

    #[Test]
    public function returns_boundary_for_linestring(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_BOUNDARY(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result'], 'boundary of linestring should return MultiPoint with zero length');
    }

    #[Test]
    public function returns_empty_for_point(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_BOUNDARY(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result'], 'boundary of point should return empty geometry with zero length');
    }
}
