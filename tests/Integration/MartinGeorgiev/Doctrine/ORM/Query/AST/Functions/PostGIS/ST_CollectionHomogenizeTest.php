<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Collect;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CollectionHomogenize;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeometryType;
use PHPUnit\Framework\Attributes\Test;

final class ST_CollectionHomogenizeTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_COLLECTIONHOMOGENIZE' => ST_CollectionHomogenize::class,
            'ST_COLLECT' => ST_Collect::class,
            'ST_GEOMETRYTYPE' => ST_GeometryType::class,
        ];
    }

    #[Test]
    public function homogenizes_collection_of_same_type_points(): void
    {
        $dql = 'SELECT ST_GEOMETRYTYPE(ST_COLLECTIONHOMOGENIZE(ST_COLLECT(g.geometry1, g.geometry2))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('ST_MultiPoint', $result[0]['result']);
    }
}
