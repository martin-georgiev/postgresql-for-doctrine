<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_IsClosed;
use PHPUnit\Framework\Attributes\Test;

final class ST_IsClosedTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ISCLOSED' => ST_IsClosed::class,
        ];
    }

    #[Test]
    public function returns_false_for_open_linestring(): void
    {
        $dql = 'SELECT ST_ISCLOSED(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
