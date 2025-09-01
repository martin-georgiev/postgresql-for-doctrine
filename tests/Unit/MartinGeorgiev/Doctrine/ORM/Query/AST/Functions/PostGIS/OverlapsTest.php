<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\Overlaps;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class OverlapsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'OVERLAPS' => Overlaps::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'checks if arrays have overlapping elements' => "SELECT (c0_.textArray && '{681,1185,1878}') AS sclr_0 FROM ContainsArrays c0_",
            'checks if geometries overlap' => 'SELECT (c0_.geometry1 && c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_',
            'checks if geometry overlaps literal' => "SELECT (c0_.geometry1 && 'POLYGON((0 0, 1 1, 2 2, 0 0))') AS sclr_0 FROM ContainsGeometries c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'checks if arrays have overlapping elements' => \sprintf("SELECT OVERLAPS(e.textArray, '{681,1185,1878}') FROM %s e", ContainsArrays::class),
            'checks if geometries overlap' => \sprintf('SELECT OVERLAPS(e.geometry1, e.geometry2) FROM %s e', ContainsGeometries::class),
            'checks if geometry overlaps literal' => \sprintf("SELECT OVERLAPS(e.geometry1, 'POLYGON((0 0, 1 1, 2 2, 0 0))') FROM %s e", ContainsGeometries::class),
        ];
    }
}
