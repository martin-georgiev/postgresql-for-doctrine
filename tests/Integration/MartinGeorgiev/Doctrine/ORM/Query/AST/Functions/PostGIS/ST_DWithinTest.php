<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_DWithin;
use PHPUnit\Framework\Attributes\Test;

final class ST_DWithinTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_DWITHIN' => ST_DWithin::class,
        ];
    }

    #[Test]
    public function returns_whether_the_geometries_are_within_the_distance_from_entity_fields(): void
    {
        $dql = 'SELECT ST_DWITHIN(g.geometry1, g.geometry2, 2.0) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_whether_the_geometries_are_within_the_distance_from_a_literal_operand(): void
    {
        $dql = "SELECT ST_DWITHIN(g.geometry1, 'SRID=4326;POINT(1 1)', 2.0) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
