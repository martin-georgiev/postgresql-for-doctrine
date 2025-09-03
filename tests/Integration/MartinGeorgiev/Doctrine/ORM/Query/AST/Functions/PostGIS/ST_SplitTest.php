<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Split;
use PHPUnit\Framework\Attributes\Test;

class ST_SplitTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_LENGTH' => ST_Length::class,
            'ST_SPLIT' => ST_Split::class,
        ];
    }

    #[Test]
    public function returns_split_geometry_when_linestring_crosses_point(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_SPLIT(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 9';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(5.656854249492381, $result[0]['result'], 0.000000000000001, 'should preserve total linestring length');
    }

    #[Test]
    public function returns_original_geometry_when_no_split_is_possible(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_SPLIT(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(2.8284271247461903, $result[0]['result'], 0.000000000000001, 'should preserve linestring length when no split occurs');
    }
}
