<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_MakeLine;
use PHPUnit\Framework\Attributes\Test;

final class ST_MakeLineTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ASTEXT' => ST_AsText::class,
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_MAKELINE' => ST_MakeLine::class,
        ];
    }

    #[Test]
    public function returns_a_line_from_wkt_literals(): void
    {
        $dql = "SELECT ST_ASTEXT(ST_MAKELINE(ST_GEOMFROMTEXT('POINT(0 0)'), ST_GEOMFROMTEXT('POINT(1 1)'))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('LINESTRING(0 0,1 1)', $result[0]['result']);
    }

    #[Test]
    public function returns_a_line_from_entity_fields(): void
    {
        $dql = 'SELECT ST_ASTEXT(ST_MAKELINE(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('LINESTRING(0 0,1 1)', $result[0]['result']);
    }
}
