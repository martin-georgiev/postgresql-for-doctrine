<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StrictlyBelow;
use PHPUnit\Framework\Attributes\Test;

class StrictlyBelowTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STRICTLY_BELOW' => StrictlyBelow::class,
        ];
    }

    #[Test]
    public function strictly_below_with_geometries(): void
    {
        $dql = 'SELECT STRICTLY_BELOW(g.geometry1, g.geometry2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g 
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsBool($result[0]['result']);
    }

    #[Test]
    public function strictly_below_with_literal_geometry(): void
    {
        $dql = "SELECT STRICTLY_BELOW(g.geometry1, 'POINT(0 5)') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g 
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsBool($result[0]['result']);
    }

    #[Test]
    public function strictly_below_with_linestrings(): void
    {
        $dql = 'SELECT STRICTLY_BELOW(g.geometry1, g.geometry2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g 
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsBool($result[0]['result']);
    }
}
