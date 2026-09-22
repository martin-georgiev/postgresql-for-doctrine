<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
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
            'ST_AREA' => ST_Area::class,
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_REMOVEIRRELEVANTPOINTSFORVIEW' => ST_RemoveIrrelevantPointsForView::class,
        ];
    }

    #[Test]
    public function returns_the_view_relevant_points_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_AREA(ST_REMOVEIRRELEVANTPOINTSFORVIEW(ST_GEOMFROMTEXT('POLYGON((0 0,0 4,4 4,4 0,0 0))'), 'BOX(-10 -10, 10 10)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result']);
    }

    #[Test]
    public function returns_the_view_relevant_points_of_an_entity_field(): void
    {
        $dql = "SELECT ST_AREA(ST_REMOVEIRRELEVANTPOINTSFORVIEW(g.geometry1, 'BOX(-10 -10, 10 10)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result']);
    }
}
