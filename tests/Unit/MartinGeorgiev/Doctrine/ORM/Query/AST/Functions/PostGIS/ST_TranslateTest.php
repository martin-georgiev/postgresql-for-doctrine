<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Translate;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class ST_TranslateTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_TRANSLATE' => ST_Translate::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ST_Translate(c0_.geometry1, 1, 1) AS sclr_0 FROM ContainsGeometries c0_',
            'SELECT ST_Translate(c0_.geometry1, ?, ?) AS sclr_0 FROM ContainsGeometries c0_',
            'SELECT ST_Translate(c0_.geometry1, MIN(1), MIN(1)) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'SELECT ST_TRANSLATE(g.geometry1, 1, 1) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'SELECT ST_TRANSLATE(g.geometry1, :dql_parameter, :dql_parameter) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'SELECT ST_TRANSLATE(g.geometry1, MIN(1), MIN(1)) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
        ];
    }
}
