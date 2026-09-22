<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineInterpolatePoint;
use PHPUnit\Framework\Attributes\Test;

final class ST_LineInterpolatePointTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ASTEXT' => ST_AsText::class,
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_LINEINTERPOLATEPOINT' => ST_LineInterpolatePoint::class,
        ];
    }

    #[Test]
    public function returns_the_interpolated_point_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_ASTEXT(ST_LINEINTERPOLATEPOINT(ST_GEOMFROMTEXT('LINESTRING(0 0,4 0)'), 0.5)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('POINT(2 0)', $result[0]['result']);
    }

    #[Test]
    public function returns_the_interpolated_point_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_ASTEXT(ST_LINEINTERPOLATEPOINT(g.geometry1, 0.5)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('POINT(1 1)', $result[0]['result']);
    }
}
