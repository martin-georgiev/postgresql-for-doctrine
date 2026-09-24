<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RowNumberOver;
use PHPUnit\Framework\Attributes\Test;

final class RowNumberOverTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ROW_NUMBER_OVER' => RowNumberOver::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'without a window specification' => 'SELECT row_number() OVER () AS sclr_0 FROM ContainsNumerics c0_',
            'with PARTITION BY' => 'SELECT row_number() OVER (PARTITION BY c0_.integer1) AS sclr_0 FROM ContainsNumerics c0_',
            'with ORDER BY' => 'SELECT row_number() OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with PARTITION BY and ORDER BY' => 'SELECT row_number() OVER (PARTITION BY c0_.integer1 ORDER BY c0_.integer2 DESC) AS sclr_0 FROM ContainsNumerics c0_',
            'with multiple PARTITION BY expressions' => 'SELECT row_number() OVER (PARTITION BY c0_.integer1, c0_.bigint1 ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with multiple ORDER BY items' => 'SELECT row_number() OVER (ORDER BY c0_.integer1 ASC, c0_.integer2 DESC) AS sclr_0 FROM ContainsNumerics c0_',
            'with an arithmetic PARTITION BY expression' => 'SELECT row_number() OVER (PARTITION BY c0_.integer1 + 1) AS sclr_0 FROM ContainsNumerics c0_',
            'with a function in PARTITION BY' => 'SELECT row_number() OVER (PARTITION BY ABS(c0_.integer1)) AS sclr_0 FROM ContainsNumerics c0_',
            'with a lowercase PARTITION keyword' => 'SELECT row_number() OVER (PARTITION BY c0_.integer1) AS sclr_0 FROM ContainsNumerics c0_',
            'next to a field and a result variable' => 'SELECT c0_.id AS id_0, row_number() OVER (ORDER BY c0_.integer2 ASC) AS sclr_1 FROM ContainsNumerics c0_ ORDER BY sclr_1 ASC',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'without a window specification' => \sprintf('SELECT ROW_NUMBER_OVER() FROM %s e', ContainsNumerics::class),
            'with PARTITION BY' => \sprintf('SELECT ROW_NUMBER_OVER(PARTITION BY e.integer1) FROM %s e', ContainsNumerics::class),
            'with ORDER BY' => \sprintf('SELECT ROW_NUMBER_OVER(ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with PARTITION BY and ORDER BY' => \sprintf('SELECT ROW_NUMBER_OVER(PARTITION BY e.integer1 ORDER BY e.integer2 DESC) FROM %s e', ContainsNumerics::class),
            'with multiple PARTITION BY expressions' => \sprintf('SELECT ROW_NUMBER_OVER(PARTITION BY e.integer1, e.bigint1 ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with multiple ORDER BY items' => \sprintf('SELECT ROW_NUMBER_OVER(ORDER BY e.integer1 ASC, e.integer2 DESC) FROM %s e', ContainsNumerics::class),
            'with an arithmetic PARTITION BY expression' => \sprintf('SELECT ROW_NUMBER_OVER(PARTITION BY e.integer1 + 1) FROM %s e', ContainsNumerics::class),
            'with a function in PARTITION BY' => \sprintf('SELECT ROW_NUMBER_OVER(PARTITION BY ABS(e.integer1)) FROM %s e', ContainsNumerics::class),
            'with a lowercase PARTITION keyword' => \sprintf('SELECT ROW_NUMBER_OVER(partition by e.integer1) FROM %s e', ContainsNumerics::class),
            'next to a field and a result variable' => \sprintf('SELECT e.id, ROW_NUMBER_OVER(ORDER BY e.integer2) AS rn FROM %s e ORDER BY rn', ContainsNumerics::class),
        ];
    }

    #[Test]
    public function throws_exception_when_argument_is_provided(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('row_number() requires exactly 0 arguments');

        $dql = \sprintf('SELECT ROW_NUMBER_OVER(e.integer1) FROM %s e', ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_partition_without_by(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('row_number() requires exactly 0 arguments');

        $dql = \sprintf('SELECT ROW_NUMBER_OVER(PARTITION e.integer1) FROM %s e', ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_order_by_before_partition_by(): void
    {
        $this->expectException(QueryException::class);

        $dql = \sprintf('SELECT ROW_NUMBER_OVER(ORDER BY e.integer2 PARTITION BY e.integer1) FROM %s e', ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
