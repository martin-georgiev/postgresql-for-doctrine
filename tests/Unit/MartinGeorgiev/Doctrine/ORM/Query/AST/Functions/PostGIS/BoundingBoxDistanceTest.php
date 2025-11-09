<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\BoundingBoxDistance;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class BoundingBoxDistanceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'BOUNDING_BOX_DISTANCE' => BoundingBoxDistance::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'calculates bounding box distance between geometries' => 'SELECT (c0_.geometry1 <#> c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'calculates bounding box distance between geometries' => \sprintf('SELECT BOUNDING_BOX_DISTANCE(e.geometry1, e.geometry2) FROM %s e', ContainsGeometries::class),
        ];
    }
}
