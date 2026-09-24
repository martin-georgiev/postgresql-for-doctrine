<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_RemoveIrrelevantPointsForView;
use PHPUnit\Framework\Attributes\Test;

final class ST_RemoveIrrelevantPointsForViewTest extends SpatialOperatorTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgisVersion(30500, 'ST_RemoveIrrelevantPointsForView');
    }

    protected function getStringFunctions(): array
    {
        return [
            'ST_REMOVEIRRELEVANTPOINTSFORVIEW' => ST_RemoveIrrelevantPointsForView::class,
            'ST_AREA' => ST_Area::class,
        ];
    }

    #[Test]
    public function preserves_area_for_simple_polygon(): void
    {
        $dql = "SELECT ST_AREA(ST_REMOVEIRRELEVANTPOINTSFORVIEW(g.geometry1, 'BOX(-10 -10, 10 10)')) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsGeometries g
                WHERE g.id = 2";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16.0, $result[0]['result']);
    }

    #[Test]
    public function preserves_area_for_simple_polygon_from_a_wkt_literal(): void
    {
        $dql = "SELECT ST_AREA(ST_REMOVEIRRELEVANTPOINTSFORVIEW('POLYGON((0 0, 0 4, 4 4, 4 0, 0 0))', 'BOX(-10 -10, 10 10)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16.0, $result[0]['result']);
    }
}
