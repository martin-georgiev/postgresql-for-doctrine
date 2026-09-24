<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Overlaps;
use PHPUnit\Framework\Attributes\Test;

final class ST_OverlapsTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_OVERLAPS' => ST_Overlaps::class,
        ];
    }

    #[Test]
    public function returns_whether_the_geometries_overlap_from_entity_fields(): void
    {
        $dql = 'SELECT ST_OVERLAPS(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_whether_the_geometries_overlap_from_a_literal_operand(): void
    {
        $dql = "SELECT ST_OVERLAPS(g.geometry1, 'SRID=4326;POINT(1 1)') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
