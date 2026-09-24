<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use PHPUnit\Framework\Attributes\Test;

final class ST_GeomFromTextTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_ASTEXT' => ST_AsText::class,
        ];
    }

    #[Test]
    public function creates_geometry_from_wkt(): void
    {
        $dql = "SELECT ST_ASTEXT(ST_GEOMFROMTEXT('POINT(1 2)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('POINT(1 2)', $result[0]['result']);
    }

    #[Test]
    public function roundtrips_a_stored_geometry(): void
    {
        $dql = 'SELECT ST_ASTEXT(ST_GEOMFROMTEXT(ST_ASTEXT(g.geometry1))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('POLYGON((0 0,0 4,4 4,4 0,0 0))', $result[0]['result']);
    }
}
