<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\StrictlyAbove;
use PHPUnit\Framework\Attributes\Test;

final class StrictlyAboveTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STRICTLY_ABOVE' => StrictlyAbove::class,
        ];
    }

    #[Test]
    public function returns_false_with_overlapping_polygons(): void
    {
        // Overlapping polygons are not strictly above each other
        $dql = 'SELECT STRICTLY_ABOVE(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
