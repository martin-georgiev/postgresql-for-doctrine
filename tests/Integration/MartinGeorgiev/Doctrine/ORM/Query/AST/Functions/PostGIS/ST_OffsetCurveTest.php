<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_OffsetCurve;
use PHPUnit\Framework\Attributes\Test;

final class ST_OffsetCurveTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_LENGTH' => ST_Length::class,
            'ST_OFFSETCURVE' => ST_OffsetCurve::class,
        ];
    }

    #[Test]
    public function returns_the_offset_curve_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_LENGTH(ST_OFFSETCURVE(ST_GEOMFROMTEXT('LINESTRING(0 0,4 0)'), 1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(4, $result[0]['result']);
    }

    #[Test]
    public function returns_the_offset_curve_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_OFFSETCURVE(g.geometry1, 1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.8284271247461903, $result[0]['result']);
    }

    #[Test]
    public function respects_the_style_argument(): void
    {
        $dql = "SELECT ST_LENGTH(ST_OFFSETCURVE(g.geometry1, 1, 'quad_segs=4 join=round')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.8284271247461903, $result[0]['result']);
    }
}
