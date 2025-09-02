<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ClipByBox2D;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class ST_ClipByBox2DTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_CLIPBYBOX2D' => ST_ClipByBox2D::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT ST_ClipByBox2D(c0_.geometry1, 'BOX(0 0, 10 10)') AS sclr_0 FROM ContainsGeometries c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'SELECT ST_CLIPBYBOX2D(g.geometry1, \'BOX(0 0, 10 10)\') FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
        ];
    }
}
