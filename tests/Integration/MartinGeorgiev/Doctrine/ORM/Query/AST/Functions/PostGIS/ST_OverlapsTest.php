<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Overlaps;
use PHPUnit\Framework\Attributes\Test;

final class ST_OverlapsTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_OVERLAPS' => ST_Overlaps::class,
        ];
    }

    #[Test]
    public function returns_whether_wkt_literals_overlap(): void
    {
        $dql = "SELECT ST_OVERLAPS(ST_GEOMFROMTEXT('POLYGON((0 0,0 2,2 2,2 0,0 0))'), ST_GEOMFROMTEXT('POLYGON((1 1,1 3,3 3,3 1,1 1))')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_whether_entity_fields_overlap(): void
    {
        $dql = 'SELECT ST_OVERLAPS(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 4';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
