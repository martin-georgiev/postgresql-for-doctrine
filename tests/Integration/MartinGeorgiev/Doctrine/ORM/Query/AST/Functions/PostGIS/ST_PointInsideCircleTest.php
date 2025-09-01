<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_PointInsideCircle;
use PHPUnit\Framework\Attributes\Test;

class ST_PointInsideCircleTest extends SpatialOperatorTestCase
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
        $dql = 'SELECT ST_PointInsideCircle(g.geometry1, 0.5, 0.5, 2.0) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_point_is_outside_circle(): void
    {
        $dql = 'SELECT ST_PointInsideCircle(g.geometry1, 10.0, 10.0, 1.0) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_point_is_inside_circle_in_where_clause(): void
    {
        $dql = 'SELECT ST_PointInsideCircle(g.geometry1, 0.5, 0.5, 2.0) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE ST_PointInsideCircle(g.geometry1, 0.5, 0.5, 2.0) = TRUE';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
