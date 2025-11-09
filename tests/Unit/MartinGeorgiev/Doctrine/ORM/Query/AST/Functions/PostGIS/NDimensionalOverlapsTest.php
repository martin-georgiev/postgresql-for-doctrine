<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\NDimensionalOverlaps;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class NDimensionalOverlapsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ND_OVERLAPS' => NDimensionalOverlaps::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'checks if geometries overlap in n-dimensions' => 'SELECT (c0_.geometry1 &&& c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_',
            'checks if geometry overlaps literal in n-dimensions' => "SELECT (c0_.geometry1 &&& 'POINT Z(1 2 3)') AS sclr_0 FROM ContainsGeometries c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'checks if geometries overlap in n-dimensions' => \sprintf('SELECT ND_OVERLAPS(e.geometry1, e.geometry2) FROM %s e', ContainsGeometries::class),
            'checks if geometry overlaps literal in n-dimensions' => \sprintf("SELECT ND_OVERLAPS(e.geometry1, 'POINT Z(1 2 3)') FROM %s e", ContainsGeometries::class),
        ];
    }
}
