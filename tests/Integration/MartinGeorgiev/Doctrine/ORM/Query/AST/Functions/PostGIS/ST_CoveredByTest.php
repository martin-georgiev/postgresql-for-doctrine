<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CoveredBy;
use PHPUnit\Framework\Attributes\Test;

final class ST_CoveredByTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_COVEREDBY' => ST_CoveredBy::class,
        ];
    }

    #[Test]
    public function returns_whether_the_geometry_is_covered_by_the_other_from_entity_fields(): void
    {
        $dql = 'SELECT ST_COVEREDBY(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_whether_the_geometry_is_covered_by_the_other_from_wkt_literals(): void
    {
        $dql = "SELECT ST_COVEREDBY('POINT(0 0)', 'POINT(1 1)') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
