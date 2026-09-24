<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use PHPUnit\Framework\Attributes\Test;

final class ST_LengthTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_LENGTH' => ST_Length::class,
        ];
    }

    #[Test]
    public function returns_length_for_linestring(): void
    {
        $dql = 'SELECT ST_LENGTH(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.8284271247461903, $result[0]['result']);
    }

    #[Test]
    public function returns_length_for_geography_linestring_with_use_spheroid(): void
    {
        $dql = "SELECT ST_LENGTH(g.geography1, 'true') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1410.1406247192313, $result[0]['result']);
    }

    #[Test]
    public function returns_length_for_linestring_from_a_wkt_literal(): void
    {
        $dql = "SELECT ST_LENGTH('LINESTRING(0 0, 1 1, 2 2)') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.8284271247461903, $result[0]['result']);
    }
}
