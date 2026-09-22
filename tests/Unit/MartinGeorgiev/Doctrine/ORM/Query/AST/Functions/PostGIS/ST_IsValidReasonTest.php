<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_IsValidReason;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_IsValidReasonTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ISVALIDREASON' => ST_IsValidReason::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'explains geometry validity' => 'SELECT ST_IsValidReason(c0_.geometry1) AS sclr_0 FROM ContainsGeometries c0_',
            'explains geometry validity with flags' => 'SELECT ST_IsValidReason(c0_.geometry1, 1) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'explains geometry validity' => \sprintf('SELECT ST_ISVALIDREASON(g.geometry1) FROM %s g', ContainsGeometries::class),
            'explains geometry validity with flags' => \sprintf('SELECT ST_ISVALIDREASON(g.geometry1, 1) FROM %s g', ContainsGeometries::class),
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('ST_IsValidReason() requires between 1 and 2 arguments');

        $dql = \sprintf('SELECT ST_ISVALIDREASON(g.geometry1, 1, 99) FROM %s g', ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
