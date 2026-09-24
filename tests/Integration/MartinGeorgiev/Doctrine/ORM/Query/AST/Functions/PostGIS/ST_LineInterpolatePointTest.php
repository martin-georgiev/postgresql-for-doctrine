<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeometryType;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineInterpolatePoint;
use PHPUnit\Framework\Attributes\Test;

final class ST_LineInterpolatePointTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMETRYTYPE' => ST_GeometryType::class,
            'ST_LINEINTERPOLATEPOINT' => ST_LineInterpolatePoint::class,
        ];
    }

    #[Test]
    public function returns_point_geometry(): void
    {
        $dql = 'SELECT ST_GEOMETRYTYPE(ST_LINEINTERPOLATEPOINT(g.geometry1, 0.5)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('ST_Point', $result[0]['result']);
    }

    #[Test]
    public function returns_point_geometry_from_a_wkt_literal(): void
    {
        $dql = "SELECT ST_GEOMETRYTYPE(ST_LINEINTERPOLATEPOINT('LINESTRING(0 0, 1 1, 2 2)', 0.5)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('ST_Point', $result[0]['result']);
    }
}
