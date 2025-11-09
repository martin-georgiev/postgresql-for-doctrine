<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Subdivide;
use PHPUnit\Framework\Attributes\Test;

class ST_SubdivideTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_EQUALS' => ST_Equals::class,
            'ST_LENGTH' => ST_Length::class,
            'ST_SUBDIVIDE' => ST_Subdivide::class,
        ];
    }

    #[Test]
    public function will_preserve_a_subdivided_polygon_area(): void
    {
        $dql = 'SELECT ST_AREA(ST_SUBDIVIDE(g.geometry1, 10)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result']);
    }

    #[Test]
    public function will_preserve_a_subdivided_linestring_length(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_SUBDIVIDE(g.geometry1, 10)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(2.8284271247461903, $result[0]['result'], 0.0000000000000001);
    }

    #[Test]
    public function returns_original_geometry_when_vertex_count_sufficient(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_SUBDIVIDE(g.geometry1, 100), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
