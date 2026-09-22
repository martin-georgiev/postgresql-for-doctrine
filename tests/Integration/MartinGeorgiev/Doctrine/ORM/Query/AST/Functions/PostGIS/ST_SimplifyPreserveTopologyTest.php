<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SimplifyPreserveTopology;
use PHPUnit\Framework\Attributes\Test;

final class ST_SimplifyPreserveTopologyTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_LENGTH' => ST_Length::class,
            'ST_SIMPLIFYPRESERVETOPOLOGY' => ST_SimplifyPreserveTopology::class,
        ];
    }

    #[Test]
    public function returns_the_topology_preserving_simplified_form_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_LENGTH(ST_SIMPLIFYPRESERVETOPOLOGY(ST_GEOMFROMTEXT('LINESTRING(0 0,1 1,2 2)'), 0.1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.8284271247461903, $result[0]['result']);
    }

    #[Test]
    public function returns_the_topology_preserving_simplified_form_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_SIMPLIFYPRESERVETOPOLOGY(g.geometry1, 0.1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.8284271247461903, $result[0]['result']);
    }
}
