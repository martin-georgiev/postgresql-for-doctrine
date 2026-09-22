<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Crosses;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use PHPUnit\Framework\Attributes\Test;

final class ST_CrossesTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_CROSSES' => ST_Crosses::class,
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
        ];
    }

    #[Test]
    public function returns_whether_wkt_literals_cross(): void
    {
        $dql = "SELECT ST_CROSSES(ST_GEOMFROMTEXT('LINESTRING(-1 -1,5 5)'), ST_GEOMFROMTEXT('POLYGON((0 0,0 4,4 4,4 0,0 0))')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_whether_entity_fields_cross(): void
    {
        $dql = 'SELECT ST_CROSSES(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 5';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
