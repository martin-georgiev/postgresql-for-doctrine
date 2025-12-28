<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_TriangulatePolygon;
use PHPUnit\Framework\Attributes\Test;

class ST_TriangulatePolygonTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_TRIANGULATEPOLYGON' => ST_TriangulatePolygon::class,
            'ST_AREA' => ST_Area::class,
        ];
    }

    #[Test]
    public function triangulates_polygon_preserving_area(): void
    {
        $dql = 'SELECT ST_AREA(ST_TRIANGULATEPOLYGON(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result'], 'triangulated polygon should preserve area of 4x4 polygon');
    }
}
