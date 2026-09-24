<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Simplify;
use PHPUnit\Framework\Attributes\Test;

final class ST_SimplifyTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_LENGTH' => ST_Length::class,
            'ST_SIMPLIFY' => ST_Simplify::class,
        ];
    }

    #[Test]
    public function returns_the_simplified_geometry_from_an_entity_field(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_SIMPLIFY(g.geometry1, 0.1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.8284271247461903, $result[0]['result']);
    }

    #[Test]
    public function returns_the_simplified_geometry_from_a_wkt_literal(): void
    {
        $dql = "SELECT ST_LENGTH(ST_SIMPLIFY('LINESTRING(0 0, 1 1, 2 2)', 0.1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.8284271247461903, $result[0]['result']);
    }
}
