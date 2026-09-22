<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Scale;
use PHPUnit\Framework\Attributes\Test;

final class ST_ScaleTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_SCALE' => ST_Scale::class,
        ];
    }

    #[Test]
    public function returns_the_scaled_form_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_AREA(ST_SCALE(ST_GEOMFROMTEXT('POLYGON((0 0,0 4,4 4,4 0,0 0))'), 1.5, 1.5)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(36, $result[0]['result']);
    }

    #[Test]
    public function returns_the_scaled_form_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_AREA(ST_SCALE(g.geometry1, 1.5, 1.5)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(36, $result[0]['result']);
    }

    #[Test]
    public function respects_the_z_factor_argument(): void
    {
        $dql = 'SELECT ST_AREA(ST_SCALE(g.geometry1, 2, 3, 15)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(96, $result[0]['result']);
    }
}
