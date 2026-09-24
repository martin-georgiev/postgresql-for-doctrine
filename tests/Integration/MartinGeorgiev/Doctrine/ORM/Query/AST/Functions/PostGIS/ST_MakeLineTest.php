<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeometryType;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_MakeLine;
use PHPUnit\Framework\Attributes\Test;

final class ST_MakeLineTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMETRYTYPE' => ST_GeometryType::class,
            'ST_MAKELINE' => ST_MakeLine::class,
        ];
    }

    #[Test]
    public function creates_the_linestring_from_entity_fields(): void
    {
        $dql = 'SELECT ST_GEOMETRYTYPE(ST_MAKELINE(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('ST_LineString', $result[0]['result']);
    }

    #[Test]
    public function creates_the_linestring_from_a_literal_operand(): void
    {
        $dql = "SELECT ST_GEOMETRYTYPE(ST_MAKELINE(g.geometry1, 'SRID=4326;POINT(1 1)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('ST_LineString', $result[0]['result']);
    }
}
