<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Distance;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Project;
use PHPUnit\Framework\Attributes\Test;

final class ST_ProjectTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_DISTANCE' => ST_Distance::class,
            'ST_EQUALS' => ST_Equals::class,
            'ST_PROJECT' => ST_Project::class,
        ];
    }

    #[Test]
    public function projects_point_with_literal_values(): void
    {
        $dql = 'SELECT ST_DISTANCE(g.geometry1, ST_PROJECT(ST_PROJECT(g.geometry1, 1000, 0.785398), 1000, 0.785398 + 3.14159)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0, $result[0]['result'], 0.01);
    }

    #[Test]
    public function projects_point_with_arithmetic_expressions(): void
    {
        $dql = 'SELECT ST_DISTANCE(ST_PROJECT(g.geometry1, 1000, 0.785398), ST_PROJECT(g.geometry1, 1000 * 2, 0.785398 + 0.1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertNotEquals(0, $result[0]['result']);
    }

    #[Test]
    public function projects_point_with_field_references(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_PROJECT(g.geometry1, 750, 0.523599), ST_PROJECT(g.geometry1, 750, 0.523599)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function projected_point_is_different_from_original(): void
    {
        $dql = 'SELECT ST_EQUALS(g.geometry1, ST_PROJECT(g.geometry1, 1000, 0)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function projects_point_zero_distance_returns_same_point(): void
    {
        $dql = 'SELECT ST_DISTANCE(g.geometry1, ST_PROJECT(g.geometry1, 0, 0)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }
}
