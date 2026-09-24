<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeometryType;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Snap;
use PHPUnit\Framework\Attributes\Test;

final class ST_SnapTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMETRYTYPE' => ST_GeometryType::class,
            'ST_SNAP' => ST_Snap::class,
        ];
    }

    #[Test]
    public function returns_snapped_geometry(): void
    {
        $dql = 'SELECT ST_GEOMETRYTYPE(ST_SNAP(g.geometry1, g.geometry2, 2.0)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('ST_Polygon', $result[0]['result']);
    }

    #[Test]
    public function returns_snapped_geometry_from_wkt_literals(): void
    {
        $dql = "SELECT ST_GEOMETRYTYPE(ST_SNAP('POLYGON((0 0, 0 4, 4 4, 4 0, 0 0))', 'POLYGON((1 1, 1 3, 3 3, 3 1, 1 1))', 2.0)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('ST_Polygon', $result[0]['result']);
    }
}
