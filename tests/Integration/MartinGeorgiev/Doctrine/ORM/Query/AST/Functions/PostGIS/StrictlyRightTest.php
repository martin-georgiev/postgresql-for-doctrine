<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\StrictlyRight;
use PHPUnit\Framework\Attributes\Test;

final class StrictlyRightTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STRICTLY_RIGHT' => StrictlyRight::class,
        ];
    }

    #[Test]
    public function returns_whether_the_bounding_box_is_strictly_right_from_entity_fields(): void
    {
        $dql = 'SELECT STRICTLY_RIGHT(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_whether_the_bounding_box_is_strictly_right_from_a_literal_operand(): void
    {
        $dql = "SELECT STRICTLY_RIGHT(g.geometry1, 'POINT(-5 -5)') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
