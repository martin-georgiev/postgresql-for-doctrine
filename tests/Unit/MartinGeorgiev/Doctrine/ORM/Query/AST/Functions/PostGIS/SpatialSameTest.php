<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\SpatialSame;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class SpatialSameTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SPATIAL_SAME' => SpatialSame::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'checks if geometries have same bounding box' => 'SELECT (c0_.geometry1 ~= c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'checks if geometries have same bounding box' => \sprintf('SELECT SPATIAL_SAME(e.geometry1, e.geometry2) FROM %s e', ContainsGeometries::class),
        ];
    }
}
