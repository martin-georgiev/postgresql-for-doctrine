<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\SpatialSame;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use PHPUnit\Framework\Attributes\Test;

final class SpatialSameTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SPATIAL_SAME' => SpatialSame::class,
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
        ];
    }

    #[Test]
    public function returns_whether_wkt_literal_bounding_boxes_are_the_same(): void
    {
        $dql = "SELECT SPATIAL_SAME(ST_GEOMFROMTEXT('POINT(1 1)'), ST_GEOMFROMTEXT('POINT(1 1)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_whether_entity_field_bounding_boxes_are_the_same(): void
    {
        $dql = 'SELECT SPATIAL_SAME(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 7';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
