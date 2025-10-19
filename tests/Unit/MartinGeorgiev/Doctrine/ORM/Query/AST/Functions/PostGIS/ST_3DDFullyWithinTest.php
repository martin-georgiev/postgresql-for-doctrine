<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_3DDFullyWithin;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class ST_3DDFullyWithinTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_3DDFULLYWITHIN' => ST_3DDFullyWithin::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ST_3DDFullyWithin(c0_.geometry1, c0_.geometry2, 1000) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'SELECT ST_3DDFULLYWITHIN(g.geometry1, g.geometry2, 1000) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
        ];
    }
}
