<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_RemoveSmallParts;
use PHPUnit\Framework\Attributes\Test;

class ST_RemoveSmallPartsTest extends SpatialOperatorTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgisVersion(30500, 'ST_RemoveSmallParts');
    }

    protected function getStringFunctions(): array
    {
        return [
            'ST_REMOVESMALLPARTS' => ST_RemoveSmallParts::class,
            'ST_AREA' => ST_Area::class,
            'ST_EQUALS' => ST_Equals::class,
        ];
    }

    #[Test]
    public function preserves_polygon_larger_than_threshold(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_REMOVESMALLPARTS(g.geometry1, 1, 0), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], '4x4 polygon (area=16) should be preserved when threshold is 1');
    }

    #[Test]
    public function removes_polygon_smaller_than_threshold(): void
    {
        $dql = 'SELECT ST_AREA(ST_REMOVESMALLPARTS(g.geometry1, 100, 0)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }
}
