<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NDimensionalBoundingBoxDistance;

class NDimensionalBoundingBoxDistanceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ND_BOUNDING_BOX_DISTANCE' => NDimensionalBoundingBoxDistance::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'calculates n-dimensional bounding box distance' => 'SELECT (c0_.geometry1 <<#>> c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'calculates n-dimensional bounding box distance' => \sprintf('SELECT ND_BOUNDING_BOX_DISTANCE(e.geometry1, e.geometry2) FROM %s e', ContainsGeometries::class),
        ];
    }
}
