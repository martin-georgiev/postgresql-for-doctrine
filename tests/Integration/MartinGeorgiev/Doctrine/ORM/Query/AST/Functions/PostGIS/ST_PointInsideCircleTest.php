<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_PointInsideCircle;
use PHPUnit\Framework\Attributes\Test;

final class ST_PointInsideCircleTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_POINTINSIDECIRCLE' => ST_PointInsideCircle::class,
        ];
    }

    #[Test]
    public function returns_true_when_point_is_inside_circle(): void
    {
        $dql = 'SELECT ST_POINTINSIDECIRCLE(g.geometry1, 0.5, 0.5, 2.0) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_point_is_inside_circle_from_a_wkt_literal(): void
    {
        $dql = "SELECT ST_POINTINSIDECIRCLE('POINT(0 0)', 0.5, 0.5, 2.0) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
