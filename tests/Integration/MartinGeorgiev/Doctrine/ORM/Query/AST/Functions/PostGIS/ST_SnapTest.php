<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Snap;
use PHPUnit\Framework\Attributes\Test;

final class ST_SnapTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_SNAP' => ST_Snap::class,
        ];
    }

    #[Test]
    public function returns_the_snapped_form_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_AREA(ST_SNAP(ST_GEOMFROMTEXT('POLYGON((0 0,0 4.1,4.1 4.1,4.1 0,0 0))'), ST_GEOMFROMTEXT('POLYGON((0 0,0 4,4 4,4 0,0 0))'), 0.5)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result']);
    }

    #[Test]
    public function returns_the_snapped_form_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_AREA(ST_SNAP(g.geometry1, g.geometry2, 2.0)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(4, $result[0]['result']);
    }
}
