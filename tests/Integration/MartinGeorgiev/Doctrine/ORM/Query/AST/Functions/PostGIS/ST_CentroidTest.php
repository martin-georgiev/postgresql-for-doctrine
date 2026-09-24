<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Centroid;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use PHPUnit\Framework\Attributes\Test;

final class ST_CentroidTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_CENTROID' => ST_Centroid::class,
            'ST_EQUALS' => ST_Equals::class,
        ];
    }

    #[Test]
    public function returns_centroid_for_point(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_CENTROID(g.geometry1), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
