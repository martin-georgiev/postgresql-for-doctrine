<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StrictlyLeft;
use PHPUnit\Framework\Attributes\Test;

class StrictlyLeftTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STRICTLY_LEFT' => StrictlyLeft::class,
        ];
    }

    #[Test]
    public function strictly_left_returns_true_when_left_geometry_is_strictly_to_the_left(): void
    {
        // POINT(0 0) is strictly to the left of POINT(1 1)
        $dql = 'SELECT STRICTLY_LEFT(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function strictly_left_returns_false_when_geometries_overlap(): void
    {
        // Overlapping polygons should return FALSE for strictly left
        $dql = 'SELECT STRICTLY_LEFT(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function strictly_left_returns_false_when_left_geometry_is_to_the_right(): void
    {
        // POINT(1 1) is NOT strictly to the left of POINT(0 0)
        $dql = 'SELECT STRICTLY_LEFT(g.geometry2, g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function strictly_left_returns_false_with_identical_geometries(): void
    {
        // Identical geometries are not strictly left of each other
        $dql = 'SELECT STRICTLY_LEFT(g.geometry1, g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
