<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeneratePoints;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_GeneratePointsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GENERATEPOINTS' => ST_GeneratePoints::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'generates random points' => 'SELECT ST_GeneratePoints(c0_.geometry1, 10) AS sclr_0 FROM ContainsGeometries c0_',
            'generates random points with seed' => 'SELECT ST_GeneratePoints(c0_.geometry1, 10, 42) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'generates random points' => 'SELECT ST_GENERATEPOINTS(g.geometry1, 10) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'generates random points with seed' => 'SELECT ST_GENERATEPOINTS(g.geometry1, 10, 42) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('ST_GeneratePoints() requires between 2 and 3 arguments');

        $dql = \sprintf('SELECT ST_GENERATEPOINTS(g.geometry1, 10, 42, 99) FROM %s g', ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
