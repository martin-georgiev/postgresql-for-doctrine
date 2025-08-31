<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\OverlapsRight;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class OverlapsRightTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'OVERLAPS_RIGHT' => OverlapsRight::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'checks if geometry overlaps or is to the right' => 'SELECT (c0_.geometry1 &> c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'checks if geometry overlaps or is to the right' => \sprintf('SELECT OVERLAPS_RIGHT(e.geometry1, e.geometry2) FROM %s e', ContainsGeometries::class),
        ];
    }
}
