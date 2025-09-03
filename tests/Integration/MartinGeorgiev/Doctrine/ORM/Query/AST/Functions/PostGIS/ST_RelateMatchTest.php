<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Relate;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_RelateMatch;
use PHPUnit\Framework\Attributes\Test;

class ST_RelateMatchTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_RELATEMATCH' => ST_RelateMatch::class,
            'ST_RELATE' => ST_Relate::class,
        ];
    }

    #[Test]
    public function returns_true_when_fixture_geometry_relation_matches_disjoint_pattern(): void
    {
        $dql = 'SELECT ST_RELATEMATCH(ST_RELATE(g.geometry1, g.geometry2), \'FF0FFF0F2\') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_fixture_geometry_relation_does_not_match_intersecting_pattern(): void
    {
        $dql = 'SELECT ST_RELATEMATCH(ST_RELATE(g.geometry1, g.geometry2), \'T*T***T**\') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_linestring_geometries_match_disjoint_pattern(): void
    {
        $dql = 'SELECT ST_RELATEMATCH(ST_RELATE(g.geometry1, g.geometry2), \'FF1FF0102\') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_point_geometries_do_not_match_linestring_pattern(): void
    {
        $dql = 'SELECT ST_RELATEMATCH(ST_RELATE(g.geometry1, g.geometry2), \'FF1FF0102\') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function can_filter_geometries_by_spatial_relationship_pattern(): void
    {
        $dql = 'SELECT g.id, ST_RELATEMATCH(ST_RELATE(g.geometry1, g.geometry2), \'FF0FFF0F2\') as matches_disjoint_points
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE ST_RELATEMATCH(ST_RELATE(g.geometry1, g.geometry2), \'FF0FFF0F2\') = TRUE';

        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
        $this->assertTrue($result[0]['matches_disjoint_points']);
        $this->assertEquals(1, $result[0]['id']);
    }

    #[Test]
    public function returns_boolean_type_when_testing_fixture_geometry_relationships(): void
    {
        $dql = 'SELECT ST_RELATEMATCH(ST_RELATE(g.geometry1, g.geometry2), \'FF0FFF0F2\') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsBool($result[0]['result']);
        $this->assertTrue($result[0]['result']);
    }
}
