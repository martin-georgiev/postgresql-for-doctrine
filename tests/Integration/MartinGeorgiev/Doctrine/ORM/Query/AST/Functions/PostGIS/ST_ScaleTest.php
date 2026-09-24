<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Scale;
use PHPUnit\Framework\Attributes\Test;

final class ST_ScaleTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_EQUALS' => ST_Equals::class,
            'ST_SCALE' => ST_Scale::class,
        ];
    }

    #[Test]
    public function returns_the_scaled_geometry_from_entity_fields(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_SCALE(g.geometry1, 2.0, 2.0), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_the_scaled_geometry_with_z_factor(): void
    {
        $dql = 'SELECT ST_AREA(ST_SCALE(g.geometry1, 2, 3, 15)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(96.0, $result[0]['result']);
    }

    #[Test]
    public function returns_the_scaled_geometry_from_a_literal_operand(): void
    {
        $dql = "SELECT ST_EQUALS(ST_SCALE(g.geometry1, 2.0, 2.0), 'SRID=4326;POINT(0 0)') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
