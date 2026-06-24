<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeneratePoints;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_NPoints;
use PHPUnit\Framework\Attributes\Test;

final class ST_GeneratePointsTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GENERATEPOINTS' => ST_GeneratePoints::class,
            'ST_NPOINTS' => ST_NPoints::class,
        ];
    }

    #[Test]
    public function generates_points_inside_polygon(): void
    {
        $dql = 'SELECT ST_NPOINTS(ST_GENERATEPOINTS(g.geometry1, 5)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(5, $result[0]['result']);
    }

    #[Test]
    public function generates_points_with_seed(): void
    {
        $dql = 'SELECT ST_NPOINTS(ST_GENERATEPOINTS(g.geometry1, 3, 42)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3, $result[0]['result']);
    }
}
