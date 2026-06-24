<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeometryType;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_OffsetCurve;
use PHPUnit\Framework\Attributes\Test;

final class ST_OffsetCurveTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMETRYTYPE' => ST_GeometryType::class,
            'ST_OFFSETCURVE' => ST_OffsetCurve::class,
        ];
    }

    #[Test]
    public function returns_offset_linestring(): void
    {
        $dql = 'SELECT ST_GEOMETRYTYPE(ST_OFFSETCURVE(g.geometry1, 1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('ST_LineString', $result[0]['result']);
    }

    #[Test]
    public function returns_offset_linestring_with_style_parameters(): void
    {
        $dql = "SELECT ST_GEOMETRYTYPE(ST_OFFSETCURVE(g.geometry1, 1, 'quad_segs=4 join=round')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('ST_LineString', $result[0]['result']);
    }
}
