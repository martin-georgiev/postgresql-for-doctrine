<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_DFullyWithin;
use PHPUnit\Framework\Attributes\Test;

final class ST_DFullyWithinTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_DFULLYWITHIN' => ST_DFullyWithin::class,
        ];
    }

    #[Test]
    public function returns_true_when_geometries_are_fully_within_distance(): void
    {
        $dql = 'SELECT ST_DFULLYWITHIN(g.geometry1, g.geometry2, 10.0) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_geometries_are_fully_within_distance_from_wkt_literals(): void
    {
        $dql = "SELECT ST_DFULLYWITHIN('POINT(0 0)', 'POINT(1 1)', 10.0) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
