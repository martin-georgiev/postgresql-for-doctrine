<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CollectionExtract;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use PHPUnit\Framework\Attributes\Test;

class ST_CollectionExtractTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_COLLECTIONEXTRACT' => ST_CollectionExtract::class,
            'ST_EQUALS' => ST_Equals::class,
        ];
    }

    #[Test]
    public function extracts_point_from_geometry(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_COLLECTIONEXTRACT(g.geometry1, 1), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function extracts_polygon_from_geometry(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_COLLECTIONEXTRACT(g.geometry1, 3), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function extracts_point_with_parameter(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_COLLECTIONEXTRACT(g.geometry1, :type), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql, ['type' => 1]);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function extracts_point_with_function_expression(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_COLLECTIONEXTRACT(g.geometry1, ABS(1)), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
