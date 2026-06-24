<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_NPoints;
use PHPUnit\Framework\Attributes\Test;

final class ST_NPointsTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_NPOINTS' => ST_NPoints::class,
        ];
    }

    #[Test]
    public function returns_point_count_for_polygon(): void
    {
        $dql = 'SELECT ST_NPOINTS(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(5, $result[0]['result']);
    }
}
