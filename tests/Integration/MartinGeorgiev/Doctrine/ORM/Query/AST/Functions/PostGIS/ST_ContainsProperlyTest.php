<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ContainsProperly;
use PHPUnit\Framework\Attributes\Test;

final class ST_ContainsProperlyTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_CONTAINSPROPERLY' => ST_ContainsProperly::class,
        ];
    }

    #[Test]
    public function returns_whether_the_geometry_contains_the_other_properly_from_entity_fields(): void
    {
        $dql = 'SELECT ST_CONTAINSPROPERLY(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_whether_the_geometry_contains_the_other_properly_from_wkt_literals(): void
    {
        $dql = "SELECT ST_CONTAINSPROPERLY('POINT(0 0)', 'POINT(1 1)') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
