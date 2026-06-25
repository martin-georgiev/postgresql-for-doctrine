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
    public function returns_perimeter_for_polygon(): void
    {
        $dql = 'SELECT ST_LENGTH(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }

    #[Test]
    public function returns_zero_for_point_as_it_has_no_length(): void
    {
        $dql = 'SELECT ST_LENGTH(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }

    #[Test]
    public function returns_actual_length_for_projected_coordinate_system_linestring(): void
    {
        $dql = 'SELECT ST_LENGTH(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 10';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2000, $result[0]['result']);
    }

    #[Test]
    public function returns_zero_for_projected_coordinate_system_polygon(): void
    {
        $dql = 'SELECT ST_LENGTH(g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 10';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
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
}
