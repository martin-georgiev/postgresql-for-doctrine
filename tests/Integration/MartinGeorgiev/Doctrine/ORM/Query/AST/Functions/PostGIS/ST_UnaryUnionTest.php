<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_UnaryUnion;
use PHPUnit\Framework\Attributes\Test;

final class ST_UnaryUnionTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_UNARYUNION' => ST_UnaryUnion::class,
        ];
    }

    #[Test]
    public function returns_the_unary_union_from_an_entity_field(): void
    {
        $dql = 'SELECT ST_AREA(ST_UNARYUNION(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result']);
    }

    #[Test]
    public function returns_the_unary_union_from_a_wkt_literal(): void
    {
        $dql = "SELECT ST_AREA(ST_UNARYUNION('POLYGON((0 0, 0 4, 4 4, 4 0, 0 0))')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result']);
    }
}
