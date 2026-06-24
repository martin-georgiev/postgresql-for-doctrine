<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineLocatePoint;
use PHPUnit\Framework\Attributes\Test;

final class ST_LineLocatePointTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_LINELOCATEPOINT' => ST_LineLocatePoint::class,
        ];
    }

    #[Test]
    public function returns_half_for_midpoint(): void
    {
        $dql = 'SELECT ST_LINELOCATEPOINT(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 9';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.5, $result[0]['result'], 0.0000000000000001);
    }
}
