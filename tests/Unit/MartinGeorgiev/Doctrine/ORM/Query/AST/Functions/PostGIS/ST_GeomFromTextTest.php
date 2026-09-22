<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_GeomFromTextTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'with WKT string literal' => "SELECT ST_GeomFromText('POINT(1 2)') AS sclr_0 FROM ContainsGeometries c0_",
            'with WKT string literal and SRID' => "SELECT ST_GeomFromText('POINT(1 2)', 4326) AS sclr_0 FROM ContainsGeometries c0_",
            'with named parameter' => 'SELECT ST_GeomFromText(?) AS sclr_0 FROM ContainsGeometries c0_',
            'with named parameter and SRID' => 'SELECT ST_GeomFromText(?, 4326) AS sclr_0 FROM ContainsGeometries c0_',
            'with field reference' => 'SELECT ST_GeomFromText(c0_.geometry1) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'with WKT string literal' => \sprintf("SELECT ST_GEOMFROMTEXT('POINT(1 2)') FROM %s g", ContainsGeometries::class),
            'with WKT string literal and SRID' => \sprintf("SELECT ST_GEOMFROMTEXT('POINT(1 2)', 4326) FROM %s g", ContainsGeometries::class),
            'with named parameter' => \sprintf('SELECT ST_GEOMFROMTEXT(:dql_parameter) FROM %s g', ContainsGeometries::class),
            'with named parameter and SRID' => \sprintf('SELECT ST_GEOMFROMTEXT(:dql_parameter, 4326) FROM %s g', ContainsGeometries::class),
            'with field reference' => \sprintf('SELECT ST_GEOMFROMTEXT(g.geometry1) FROM %s g', ContainsGeometries::class),
        ];
    }
}
