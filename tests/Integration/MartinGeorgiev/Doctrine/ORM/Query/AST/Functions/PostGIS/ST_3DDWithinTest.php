<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_3DDWithin;
use PHPUnit\Framework\Attributes\Test;

class ST_3DDWithinTest extends SpatialOperatorTestCase
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
    public function returns_false_when_3d_geometries_are_not_within_distance(): void
    {
        $dql = 'SELECT ST_3DDWITHIN(g.geometry1, g.geometry2, 0.1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_3d_geometries_are_within_distance_in_where_clause(): void
    {
        $dql = 'SELECT ST_3DDWITHIN(g.geometry1, g.geometry2, 10.0) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE ST_3DDWITHIN(g.geometry1, g.geometry2, 10.0) = TRUE';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
