<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_NPoints;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Segmentize;
use PHPUnit\Framework\Attributes\Test;

final class ST_SegmentizeTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_NPOINTS' => ST_NPoints::class,
            'ST_SEGMENTIZE' => ST_Segmentize::class,
        ];
    }

    #[Test]
    public function increases_point_count_on_linestring(): void
    {
        $dql = 'SELECT ST_NPOINTS(g.geometry1) as original,
        $              ST_NPOINTS(ST_SEGMENTIZE(g.geometry1, 0.5)) as segmentized
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['segmentized'] > $result[0]['original']);
    }
}
