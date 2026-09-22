<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_NumGeometries;
use PHPUnit\Framework\Attributes\Test;

final class ST_NumGeometriesTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_NUMGEOMETRIES' => ST_NumGeometries::class,
        ];
    }

    #[Test]
    public function returns_the_geometry_count_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_NUMGEOMETRIES(ST_GEOMFROMTEXT('MULTIPOINT((1 2),(3 4))')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2, $result[0]['result']);
    }

    #[Test]
    public function returns_the_geometry_count_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_NUMGEOMETRIES(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1, $result[0]['result']);
    }
}
