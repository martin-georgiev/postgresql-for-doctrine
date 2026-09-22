<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_NPoints;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Segmentize;
use PHPUnit\Framework\Attributes\Test;

final class ST_SegmentizeTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_NPOINTS' => ST_NPoints::class,
            'ST_SEGMENTIZE' => ST_Segmentize::class,
        ];
    }

    #[Test]
    public function returns_the_segmentized_form_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_NPOINTS(ST_SEGMENTIZE(ST_GEOMFROMTEXT('LINESTRING(0 0,2 0)'), 0.5)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(5, $result[0]['result']);
    }

    #[Test]
    public function returns_the_segmentized_form_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_NPOINTS(ST_SEGMENTIZE(g.geometry1, 0.5)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(7, $result[0]['result']);
    }
}
