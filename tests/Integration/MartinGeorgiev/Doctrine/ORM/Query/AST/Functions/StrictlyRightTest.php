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
        $this->assertIsBool($result[0]['result']);
    }

    #[Test]
    public function strictly_right_with_literal_geometry(): void
    {
        $dql = "SELECT STRICTLY_RIGHT(g.geometry1, 'POINT(-5 -5)') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g 
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsBool($result[0]['result']);
    }

    #[Test]
    public function strictly_right_with_linestrings(): void
    {
        $dql = 'SELECT STRICTLY_RIGHT(g.geometry2, g.geometry1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g 
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsBool($result[0]['result']);
    }
}
