<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_3DDWithin;
use PHPUnit\Framework\Attributes\Test;

final class ST_3DDWithinTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_3DDWITHIN' => ST_3DDWithin::class,
        ];
    }

    #[Test]
    public function returns_true_when_3d_geometries_are_within_distance(): void
    {
        $dql = 'SELECT ST_3DDWITHIN(g.geometry1, g.geometry2, 10.0) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_3d_geometries_are_within_distance_from_wkt_literals(): void
    {
        $dql = "SELECT ST_3DDWITHIN('POINT(0 0)', 'POINT(1 1)', 10.0) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
