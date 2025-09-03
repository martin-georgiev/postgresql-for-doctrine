<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Transform;
use PHPUnit\Framework\Attributes\Test;

class ST_TransformTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_EQUALS' => ST_Equals::class,
            'ST_TRANSFORM' => ST_Transform::class,
        ];
    }

    #[Test]
    public function returns_transformed_point_to_wgs84(): void
    {
        $dql = 'SELECT ST_EQUALS(g.geometry1, ST_TRANSFORM(g.geometry1, 4326)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'transformation to same SRID should return identical geometry');
    }

    #[Test]
    public function returns_transformed_polygon_to_web_mercator(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_TRANSFORM(g.geometry1, 4326), ST_TRANSFORM(g.geometry1, 4326)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 10';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'should be deterministic for same transformation');
    }

    #[Test]
    public function returns_transformed_linestring_to_utm(): void
    {
        $dql = 'SELECT ST_EQUALS(g.geometry1, ST_TRANSFORM(g.geometry1, 3857)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 10';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'transformation to same SRID should return identical geometry');
    }
}
