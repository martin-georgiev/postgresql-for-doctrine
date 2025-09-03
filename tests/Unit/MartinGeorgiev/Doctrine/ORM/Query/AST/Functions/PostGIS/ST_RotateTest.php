<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Rotate;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class ST_RotateTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ROTATE' => ST_Rotate::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ST_Rotate(c0_.geometry1, 0.785398) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'SELECT ST_ROTATE(g.geometry1, 0.785398) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
        ];
    }
}
