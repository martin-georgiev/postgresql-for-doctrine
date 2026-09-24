<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NtileOver;
use PHPUnit\Framework\Attributes\Test;

final class NtileOverTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'NTILE_OVER' => NtileOver::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'without a window specification' => 'SELECT ntile(4) OVER () AS sclr_0 FROM ContainsNumerics c0_',
            'with PARTITION BY' => 'SELECT ntile(4) OVER (PARTITION BY c0_.integer1) AS sclr_0 FROM ContainsNumerics c0_',
            'with ORDER BY' => 'SELECT ntile(4) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with PARTITION BY and ORDER BY' => 'SELECT ntile(4) OVER (PARTITION BY c0_.integer1 ORDER BY c0_.integer2 DESC) AS sclr_0 FROM ContainsNumerics c0_',
            'with multiple PARTITION BY expressions' => 'SELECT ntile(4) OVER (PARTITION BY c0_.integer1, c0_.bigint1 ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with an entity field as the bucket count' => 'SELECT ntile(c0_.integer1) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with a parameter as the bucket count' => 'SELECT ntile(?) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with only a frame after the bucket count' => 'SELECT ntile(4) OVER (ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS sclr_0 FROM ContainsNumerics c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'without a window specification' => \sprintf('SELECT NTILE_OVER(4) FROM %s e', ContainsNumerics::class),
            'with PARTITION BY' => \sprintf('SELECT NTILE_OVER(4, PARTITION BY e.integer1) FROM %s e', ContainsNumerics::class),
            'with ORDER BY' => \sprintf('SELECT NTILE_OVER(4, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with PARTITION BY and ORDER BY' => \sprintf('SELECT NTILE_OVER(4, PARTITION BY e.integer1 ORDER BY e.integer2 DESC) FROM %s e', ContainsNumerics::class),
            'with multiple PARTITION BY expressions' => \sprintf('SELECT NTILE_OVER(4, PARTITION BY e.integer1, e.bigint1 ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with an entity field as the bucket count' => \sprintf('SELECT NTILE_OVER(e.integer1, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with a parameter as the bucket count' => \sprintf('SELECT NTILE_OVER(:buckets, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with only a frame after the bucket count' => \sprintf('SELECT NTILE_OVER(4, ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) FROM %s e', ContainsNumerics::class),
        ];
    }

    #[Test]
    public function throws_exception_for_missing_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('ntile() requires exactly 1 argument');

        $dql = \sprintf('SELECT NTILE_OVER() FROM %s e', ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_window_specification_without_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('ntile() requires exactly 1 argument');

        $dql = \sprintf('SELECT NTILE_OVER(PARTITION BY e.integer1) FROM %s e', ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('ntile() requires exactly 1 argument');

        $dql = \sprintf('SELECT NTILE_OVER(4, 2, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_window_specification_without_a_separating_comma(): void
    {
        $this->expectException(QueryException::class);

        $dql = \sprintf('SELECT NTILE_OVER(4 ORDER BY e.integer2) FROM %s e', ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
