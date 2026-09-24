<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Relate;
use PHPUnit\Framework\Attributes\Test;

final class ST_RelateTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_RELATE' => ST_Relate::class,
        ];
    }

    #[Test]
    public function returns_the_intersection_matrix_from_entity_fields(): void
    {
        $dql = 'SELECT ST_RELATE(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('FF0FFF0F2', $result[0]['result']);
    }

    #[Test]
    public function returns_true_when_geometries_match_disjoint_pattern(): void
    {
        $dql = 'SELECT ST_RELATE(g.geometry1, g.geometry2, \'FF0FFF0F2\') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_the_intersection_matrix_from_a_literal_operand(): void
    {
        $dql = "SELECT ST_RELATE(g.geometry1, 'SRID=4326;POINT(1 1)') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('FF0FFF0F2', $result[0]['result']);
    }
}
