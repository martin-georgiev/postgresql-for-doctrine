<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_RemoveSmallParts;
use PHPUnit\Framework\Attributes\Test;

final class ST_RemoveSmallPartsTest extends SpatialOperatorTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgisVersion(30500, 'ST_RemoveSmallParts');
    }

    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_REMOVESMALLPARTS' => ST_RemoveSmallParts::class,
        ];
    }

    #[Test]
    public function returns_the_large_parts_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_AREA(ST_REMOVESMALLPARTS(ST_GEOMFROMTEXT('POLYGON((0 0,0 4,4 4,4 0,0 0))'), 1, 0)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result']);
    }

    #[Test]
    public function returns_the_large_parts_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_AREA(ST_REMOVESMALLPARTS(g.geometry1, 1, 0)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result']);
    }
}
