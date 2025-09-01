<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_DFullyWithin;
use PHPUnit\Framework\Attributes\Test;

class ST_DFullyWithinTest extends SpatialOperatorTestCase
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
    public function returns_false_when_geometries_are_not_fully_within_distance(): void
    {
        $dql = 'SELECT ST_DFULLYWITHIN(g.geometry1, g.geometry2, 0.1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_geometries_are_fully_within_distance_in_where_clause(): void
    {
        $dql = 'SELECT ST_DFULLYWITHIN(g.geometry1, g.geometry2, 10.0) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE ST_DFULLYWITHIN(g.geometry1, g.geometry2, 10.0) = TRUE';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
