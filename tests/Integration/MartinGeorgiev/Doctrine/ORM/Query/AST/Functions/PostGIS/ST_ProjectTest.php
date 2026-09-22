<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Distance;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Project;
use PHPUnit\Framework\Attributes\Test;

final class ST_ProjectTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_DISTANCE' => ST_Distance::class,
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_PROJECT' => ST_Project::class,
        ];
    }

    #[Test]
    public function returns_the_projected_point_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_DISTANCE(ST_GEOMFROMTEXT('POINT(0 0)', 4326), ST_PROJECT(ST_GEOMFROMTEXT('POINT(0 0)', 4326), 1000, 0)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1000, $result[0]['result'], 0.000001);
    }

    #[Test]
    public function returns_the_projected_point_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_DISTANCE(g.geometry1, ST_PROJECT(g.geometry1, 1000, 0)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1000, $result[0]['result'], 0.000001);
    }
}
