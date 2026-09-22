<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Difference;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ExteriorRing;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use PHPUnit\Framework\Attributes\Test;

final class ST_ExteriorRingTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_EXTERIORRING' => ST_ExteriorRing::class,
            'ST_DIFFERENCE' => ST_Difference::class,
            'ST_LENGTH' => ST_Length::class,
        ];
    }

    #[Test]
    public function returns_ring_with_the_perimeter_of_the_polygon(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_EXTERIORRING(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(16.0, $result[0]['result'], 0.0001);
    }

    #[Test]
    public function returns_null_for_linestring(): void
    {
        $dql = 'SELECT ST_EXTERIORRING(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }
}
