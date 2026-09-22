<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use PHPUnit\Framework\Attributes\Test;

final class ST_AsTextTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ASTEXT' => ST_AsText::class,
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
        ];
    }

    #[Test]
    public function returns_the_wkt_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_ASTEXT(ST_GEOMFROMTEXT('POINT(-9.1393 38.7223)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('POINT(-9.1393 38.7223)', $result[0]['result']);
    }

    #[Test]
    public function returns_the_wkt_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_ASTEXT(g.geography1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('POINT(-9.1393 38.7223)', $result[0]['result']);
    }

    #[Test]
    public function respects_max_decimal_digits(): void
    {
        $dql = 'SELECT ST_ASTEXT(g.geography1, 2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('POINT(-9.14 38.72)', $result[0]['result']);
    }
}
