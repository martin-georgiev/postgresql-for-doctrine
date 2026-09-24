<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Split;
use PHPUnit\Framework\Attributes\Test;

final class ST_SplitTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_LENGTH' => ST_Length::class,
            'ST_SPLIT' => ST_Split::class,
        ];
    }

    #[Test]
    public function returns_the_split_geometry_from_entity_fields(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_SPLIT(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 9';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(5.656854249492381, $result[0]['result']);
    }

    #[Test]
    public function returns_the_split_geometry_from_wkt_literals(): void
    {
        $dql = "SELECT ST_LENGTH(ST_SPLIT('LINESTRING(0 0, 4 4)', 'POINT(2 2)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 9";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(5.656854249492381, $result[0]['result']);
    }
}
