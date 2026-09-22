<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_PointInsideCircle;
use PHPUnit\Framework\Attributes\Test;

final class ST_PointInsideCircleTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_POINTINSIDECIRCLE' => ST_PointInsideCircle::class,
        ];
    }

    #[Test]
    public function returns_whether_a_wkt_literal_is_inside_a_circle(): void
    {
        $dql = "SELECT ST_POINTINSIDECIRCLE(ST_GEOMFROMTEXT('POINT(0 0)'), 0.5, 0.5, 2.0) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_whether_an_entity_field_is_inside_a_circle(): void
    {
        $dql = 'SELECT ST_POINTINSIDECIRCLE(g.geometry1, 0.5, 0.5, 2.0) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
