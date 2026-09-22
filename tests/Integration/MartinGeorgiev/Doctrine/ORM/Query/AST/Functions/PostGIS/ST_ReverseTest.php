<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Reverse;
use PHPUnit\Framework\Attributes\Test;

final class ST_ReverseTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ASTEXT' => ST_AsText::class,
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_REVERSE' => ST_Reverse::class,
        ];
    }

    #[Test]
    public function returns_the_reverse_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_ASTEXT(ST_REVERSE(ST_GEOMFROMTEXT('LINESTRING(0 0,1 1,2 2)'))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('LINESTRING(2 2,1 1,0 0)', $result[0]['result']);
    }

    #[Test]
    public function returns_the_reverse_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_ASTEXT(ST_REVERSE(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('LINESTRING(2 2,1 1,0 0)', $result[0]['result']);
    }
}
