<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StrictlyRight;
use PHPUnit\Framework\Attributes\Test;

class StrictlyRightTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STRICTLY_RIGHT' => StrictlyRight::class,
        ];
    }

    #[Test]
    public function strictly_right_with_geometries(): void
    {
        $dql = 'SELECT STRICTLY_RIGHT(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result'], 'POINT(1 1) may not be strictly to the right of POINT(0 0) depending on PostGIS strictness');
    }

    #[Test]
    public function strictly_right_with_literal_geometry(): void
    {
        $dql = "SELECT STRICTLY_RIGHT(g.geometry1, 'POINT(-5 -5)') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g 
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'POINT(0 0) should be strictly to the right of POINT(-5 -5)');
    }

    #[Test]
    public function strictly_right_with_linestrings(): void
    {
        $dql = 'SELECT STRICTLY_RIGHT(g.geometry2, g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'Higher linestring should be strictly to the right of lower linestring');
    }
}
