<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeometryType;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineInterpolatePoint;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_X;
use PHPUnit\Framework\Attributes\Test;

final class ST_LineInterpolatePointTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMETRYTYPE' => ST_GeometryType::class,
            'ST_LINEINTERPOLATEPOINT' => ST_LineInterpolatePoint::class,
            'ST_X' => ST_X::class,
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
    public function returns_midpoint_of_linestring(): void
    {
        $dql = 'SELECT ST_X(ST_LINEINTERPOLATEPOINT(g.geometry1, 0.5)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.0, $result[0]['result']);
    }
}
