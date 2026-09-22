<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Touches;
use PHPUnit\Framework\Attributes\Test;

final class ST_TouchesTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_TOUCHES' => ST_Touches::class,
        ];
    }

    #[Test]
    public function returns_whether_wkt_literals_touch(): void
    {
        $dql = "SELECT ST_TOUCHES(ST_GEOMFROMTEXT('POLYGON((0 0,0 2,2 2,2 0,0 0))'), ST_GEOMFROMTEXT('POLYGON((2 0,2 2,4 2,4 0,2 0))')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_whether_entity_fields_touch(): void
    {
        $dql = 'SELECT ST_TOUCHES(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 8';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
