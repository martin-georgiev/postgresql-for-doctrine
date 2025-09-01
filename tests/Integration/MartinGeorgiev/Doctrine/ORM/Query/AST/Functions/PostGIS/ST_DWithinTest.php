<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_DWithin;
use PHPUnit\Framework\Attributes\Test;

class ST_DWithinTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_DWITHIN' => ST_DWithin::class,
        ];
    }

    #[Test]
    public function returns_true_when_points_are_within_distance(): void
    {
        $dql = 'SELECT ST_DWITHIN(g.geometry1, g.geometry2, 2.0) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_points_are_not_within_distance(): void
    {
        $dql = 'SELECT ST_DWITHIN(g.geometry1, g.geometry2, 0.5) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_identical_geometries(): void
    {
        $dql = 'SELECT ST_DWITHIN(g.geometry1, g.geometry1, 0.0) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
