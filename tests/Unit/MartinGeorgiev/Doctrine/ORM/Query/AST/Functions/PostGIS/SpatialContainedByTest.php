<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\SpatialContainedBy;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class SpatialContainedByTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SPATIAL_CONTAINED_BY' => SpatialContainedBy::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'checks if geometry is spatially contained by another' => 'SELECT (c0_.geometry1 @ c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'checks if geometry is spatially contained by another' => \sprintf('SELECT SPATIAL_CONTAINED_BY(e.geometry1, e.geometry2) FROM %s e', ContainsGeometries::class),
        ];
    }
}
