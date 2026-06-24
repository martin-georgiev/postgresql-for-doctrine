<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_NumGeometries;
use PHPUnit\Framework\Attributes\Test;

final class ST_NumGeometriesTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_NUMGEOMETRIES' => ST_NumGeometries::class,
        ];
    }

    #[Test]
    public function returns_one_for_single_geometry(): void
    {
        $dql = 'SELECT ST_NUMGEOMETRIES(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1, $result[0]['result']);
    }
}
