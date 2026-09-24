<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\StrictlyLeft;
use PHPUnit\Framework\Attributes\Test;

final class StrictlyLeftTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STRICTLY_LEFT' => StrictlyLeft::class,
        ];
    }

    #[Test]
    public function returns_true_when_left_geometry_is_strictly_to_the_left(): void
    {
        $dql = 'SELECT STRICTLY_LEFT(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_left_geometry_is_strictly_to_the_left_from_a_literal_operand(): void
    {
        $dql = "SELECT STRICTLY_LEFT(g.geometry1, 'SRID=4326;POINT(1 1)') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
