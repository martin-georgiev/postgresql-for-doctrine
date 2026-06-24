<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_MakePoint;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_MakePointTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_MAKEPOINT' => ST_MakePoint::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'creates 2D point' => 'SELECT ST_MakePoint(1, 2) AS sclr_0 FROM ContainsGeometries c0_',
            'creates 3DZ point' => 'SELECT ST_MakePoint(1, 2, 3) AS sclr_0 FROM ContainsGeometries c0_',
            'creates 4D point' => 'SELECT ST_MakePoint(1, 2, 3, 4) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'creates 2D point' => 'SELECT ST_MAKEPOINT(1, 2) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'creates 3DZ point' => 'SELECT ST_MAKEPOINT(1, 2, 3) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'creates 4D point' => 'SELECT ST_MAKEPOINT(1, 2, 3, 4) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('ST_MakePoint() requires between 2 and 4 arguments');

        $dql = \sprintf('SELECT ST_MAKEPOINT(1, 2, 3, 4, 5) FROM %s g', ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
