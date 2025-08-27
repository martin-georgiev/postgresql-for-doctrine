<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SpatialContains;

class SpatialContainsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SPATIAL_CONTAINS' => SpatialContains::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'checks if geometry spatially contains another' => 'SELECT (c0_.geometry1 ~ c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_',
            'checks if geometry spatially contains literal' => "SELECT (c0_.geometry1 ~ 'POINT(1 2)') AS sclr_0 FROM ContainsGeometries c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'checks if geometry spatially contains another' => \sprintf('SELECT SPATIAL_CONTAINS(e.geometry1, e.geometry2) FROM %s e', ContainsGeometries::class),
            'checks if geometry spatially contains literal' => \sprintf("SELECT SPATIAL_CONTAINS(e.geometry1, 'POINT(1 2)') FROM %s e", ContainsGeometries::class),
        ];
    }
}
