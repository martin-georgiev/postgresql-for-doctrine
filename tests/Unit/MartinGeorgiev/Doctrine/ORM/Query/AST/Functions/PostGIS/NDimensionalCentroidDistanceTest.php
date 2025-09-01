<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\NDimensionalCentroidDistance;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class NDimensionalCentroidDistanceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ND_CENTROID_DISTANCE' => NDimensionalCentroidDistance::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'calculates n-dimensional centroid distance' => 'SELECT (c0_.geometry1 <<->> c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'calculates n-dimensional centroid distance' => \sprintf('SELECT ND_CENTROID_DISTANCE(e.geometry1, e.geometry2) FROM %s e', ContainsGeometries::class),
        ];
    }
}
