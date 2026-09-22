<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Relate;
use PHPUnit\Framework\Attributes\Test;

final class ST_RelateTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_RELATE' => ST_Relate::class,
        ];
    }

    #[Test]
    public function returns_the_de9im_matrix_of_wkt_literals(): void
    {
        $dql = "SELECT ST_RELATE(ST_GEOMFROMTEXT('POINT(0 0)'), ST_GEOMFROMTEXT('POINT(1 1)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('FF0FFF0F2', $result[0]['result']);
    }

    #[Test]
    public function returns_the_de9im_matrix_of_entity_fields(): void
    {
        $dql = 'SELECT ST_RELATE(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('FF0FFF0F2', $result[0]['result']);
    }

    #[Test]
    public function respects_the_intersection_pattern_argument(): void
    {
        $dql = "SELECT ST_RELATE(g.geometry1, g.geometry2, 'FF0FFF0F2') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
