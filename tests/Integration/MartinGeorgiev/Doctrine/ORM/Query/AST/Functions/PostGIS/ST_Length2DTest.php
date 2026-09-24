<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length2D;
use PHPUnit\Framework\Attributes\Test;

final class ST_Length2DTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_LENGTH2D' => ST_Length2D::class,
        ];
    }

    #[Test]
    public function returns_the_2d_length_from_an_entity_field(): void
    {
        $dql = 'SELECT ST_LENGTH2D(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.8284271247461903, $result[0]['result']);
    }

    #[Test]
    public function returns_the_2d_length_from_a_wkt_literal(): void
    {
        $dql = "SELECT ST_LENGTH2D('LINESTRING(0 0, 1 1, 2 2)') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.8284271247461903, $result[0]['result']);
    }
}
