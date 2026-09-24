<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Distance;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Translate;
use PHPUnit\Framework\Attributes\Test;

final class ST_TranslateTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_DISTANCE' => ST_Distance::class,
            'ST_TRANSLATE' => ST_Translate::class,
        ];
    }

    #[Test]
    public function returns_the_translated_geometry_from_entity_fields(): void
    {
        $dql = 'SELECT ST_DISTANCE(g.geometry1, ST_TRANSLATE(g.geometry1, 10.0, 10.0)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(14.142135623730951, $result[0]['result']);
    }

    #[Test]
    public function returns_the_translated_geometry_with_delta_z(): void
    {
        $dql = 'SELECT ST_AREA(ST_TRANSLATE(g.geometry1, 1, 2, 3)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result']);
    }

    #[Test]
    public function returns_the_translated_geometry_from_a_literal_operand(): void
    {
        $dql = "SELECT ST_DISTANCE('SRID=4326;POINT(0 0)', ST_TRANSLATE(g.geometry1, 10.0, 10.0)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(14.142135623730951, $result[0]['result']);
    }
}
