<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Point;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_PointTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_POINT' => ST_Point::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'creates point from coordinates' => 'SELECT ST_Point(1, 2) AS sclr_0 FROM ContainsGeometries c0_',
            'creates point with SRID' => 'SELECT ST_Point(1, 2, 4326) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'creates point from coordinates' => 'SELECT ST_POINT(1, 2) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'creates point with SRID' => 'SELECT ST_POINT(1, 2, 4326) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('ST_Point() requires between 2 and 3 arguments');

        $dql = \sprintf('SELECT ST_POINT(1, 2, 4326, 99) FROM %s g', ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
