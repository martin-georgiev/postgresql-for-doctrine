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
            'clips geometry with literal box' => "SELECT ST_ClipByBox2D(c0_.geometry1, 'BOX(0 0, 4 4)') AS sclr_0 FROM ContainsGeometries c0_",
            'clips geometry with parameter placeholder' => 'SELECT ST_ClipByBox2D(c0_.geometry1, ?) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'clips geometry with literal box' => 'SELECT ST_CLIPBYBOX2D(g.geometry1, \'BOX(0 0, 4 4)\') FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'clips geometry with parameter placeholder' => 'SELECT ST_CLIPBYBOX2D(g.geometry1, :box_param) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
        ];
    }
}
