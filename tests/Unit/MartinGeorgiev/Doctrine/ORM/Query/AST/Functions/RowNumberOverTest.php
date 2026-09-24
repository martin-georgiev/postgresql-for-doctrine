<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RowNumberOver;
use PHPUnit\Framework\Attributes\DataProvider;
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
<<<<<<< HEAD
            'with a ROWS frame start' => 'SELECT row_number() OVER (ORDER BY c0_.integer2 ASC ROWS UNBOUNDED PRECEDING) AS sclr_0 FROM ContainsNumerics c0_',
            'with a ROWS frame between bounds' => 'SELECT row_number() OVER (ORDER BY c0_.integer2 ASC ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS sclr_0 FROM ContainsNumerics c0_',
            'with a RANGE frame' => 'SELECT row_number() OVER (ORDER BY c0_.integer2 ASC RANGE BETWEEN CURRENT ROW AND UNBOUNDED FOLLOWING) AS sclr_0 FROM ContainsNumerics c0_',
            'with a GROUPS frame' => 'SELECT row_number() OVER (ORDER BY c0_.integer2 ASC GROUPS BETWEEN 1 PRECEDING AND 1 FOLLOWING) AS sclr_0 FROM ContainsNumerics c0_',
            'with an integer offset frame start' => 'SELECT row_number() OVER (ORDER BY c0_.integer2 ASC ROWS 2 PRECEDING) AS sclr_0 FROM ContainsNumerics c0_',
            'with a CURRENT ROW frame start' => 'SELECT row_number() OVER (ORDER BY c0_.integer2 ASC ROWS CURRENT ROW) AS sclr_0 FROM ContainsNumerics c0_',
            'with parameter frame offsets' => 'SELECT row_number() OVER (ORDER BY c0_.integer2 ASC ROWS BETWEEN ? PRECEDING AND ? FOLLOWING) AS sclr_0 FROM ContainsNumerics c0_',
            'with an interval frame offset' => "SELECT row_number() OVER (ORDER BY c0_.integer2 ASC RANGE BETWEEN '1 day' PRECEDING AND CURRENT ROW) AS sclr_0 FROM ContainsNumerics c0_",
            'with a decimal frame offset' => 'SELECT row_number() OVER (ORDER BY c0_.integer2 ASC RANGE BETWEEN 0.5 PRECEDING AND 0.5 FOLLOWING) AS sclr_0 FROM ContainsNumerics c0_',
            'with a frame and no ORDER BY' => 'SELECT row_number() OVER (ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING) AS sclr_0 FROM ContainsNumerics c0_',
            'with PARTITION BY and a frame' => 'SELECT row_number() OVER (PARTITION BY c0_.integer1 ROWS UNBOUNDED PRECEDING) AS sclr_0 FROM ContainsNumerics c0_',
            'with a frame excluding the current row' => 'SELECT row_number() OVER (ORDER BY c0_.integer2 ASC ROWS BETWEEN 1 PRECEDING AND 1 FOLLOWING EXCLUDE CURRENT ROW) AS sclr_0 FROM ContainsNumerics c0_',
            'with a frame excluding the group' => 'SELECT row_number() OVER (ORDER BY c0_.integer2 ASC GROUPS BETWEEN 1 PRECEDING AND 1 FOLLOWING EXCLUDE GROUP) AS sclr_0 FROM ContainsNumerics c0_',
            'with a frame excluding ties' => 'SELECT row_number() OVER (ORDER BY c0_.integer2 ASC RANGE BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW EXCLUDE TIES) AS sclr_0 FROM ContainsNumerics c0_',
            'with a frame excluding no others' => 'SELECT row_number() OVER (ORDER BY c0_.integer2 ASC ROWS UNBOUNDED PRECEDING EXCLUDE NO OTHERS) AS sclr_0 FROM ContainsNumerics c0_',
            'with lowercase frame keywords' => 'SELECT row_number() OVER (ORDER BY c0_.integer2 ASC ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW EXCLUDE GROUP) AS sclr_0 FROM ContainsNumerics c0_',
            'next to an entity and a result variable' => 'SELECT c0_.id AS id_0, c0_.integer1 AS integer1_1, c0_.integer2 AS integer2_2, c0_.bigint1 AS bigint1_3, c0_.bigint2 AS bigint2_4, c0_.decimal1 AS decimal1_5, c0_.decimal2 AS decimal2_6, row_number() OVER (ORDER BY c0_.integer2 ASC) AS sclr_7 FROM ContainsNumerics c0_ ORDER BY sclr_7 ASC',
=======
            'next to a field and a result variable' => 'SELECT c0_.id AS id_0, row_number() OVER (ORDER BY c0_.integer2 ASC) AS sclr_1 FROM ContainsNumerics c0_ ORDER BY sclr_1 ASC',
>>>>>>> origin/feat/window-value-functions
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
<<<<<<< HEAD
            'with a ROWS frame start' => \sprintf('SELECT ROW_NUMBER_OVER(ORDER BY e.integer2 ROWS UNBOUNDED PRECEDING) FROM %s e', ContainsNumerics::class),
            'with a ROWS frame between bounds' => \sprintf('SELECT ROW_NUMBER_OVER(ORDER BY e.integer2 ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) FROM %s e', ContainsNumerics::class),
            'with a RANGE frame' => \sprintf('SELECT ROW_NUMBER_OVER(ORDER BY e.integer2 RANGE BETWEEN CURRENT ROW AND UNBOUNDED FOLLOWING) FROM %s e', ContainsNumerics::class),
            'with a GROUPS frame' => \sprintf('SELECT ROW_NUMBER_OVER(ORDER BY e.integer2 GROUPS BETWEEN 1 PRECEDING AND 1 FOLLOWING) FROM %s e', ContainsNumerics::class),
            'with an integer offset frame start' => \sprintf('SELECT ROW_NUMBER_OVER(ORDER BY e.integer2 ROWS 2 PRECEDING) FROM %s e', ContainsNumerics::class),
            'with a CURRENT ROW frame start' => \sprintf('SELECT ROW_NUMBER_OVER(ORDER BY e.integer2 ROWS CURRENT ROW) FROM %s e', ContainsNumerics::class),
            'with parameter frame offsets' => \sprintf('SELECT ROW_NUMBER_OVER(ORDER BY e.integer2 ROWS BETWEEN :before PRECEDING AND :after FOLLOWING) FROM %s e', ContainsNumerics::class),
            'with an interval frame offset' => \sprintf("SELECT ROW_NUMBER_OVER(ORDER BY e.integer2 RANGE BETWEEN '1 day' PRECEDING AND CURRENT ROW) FROM %s e", ContainsNumerics::class),
            'with a decimal frame offset' => \sprintf('SELECT ROW_NUMBER_OVER(ORDER BY e.integer2 RANGE BETWEEN 0.5 PRECEDING AND 0.5 FOLLOWING) FROM %s e', ContainsNumerics::class),
            'with a frame and no ORDER BY' => \sprintf('SELECT ROW_NUMBER_OVER(ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING) FROM %s e', ContainsNumerics::class),
            'with PARTITION BY and a frame' => \sprintf('SELECT ROW_NUMBER_OVER(PARTITION BY e.integer1 ROWS UNBOUNDED PRECEDING) FROM %s e', ContainsNumerics::class),
            'with a frame excluding the current row' => \sprintf('SELECT ROW_NUMBER_OVER(ORDER BY e.integer2 ROWS BETWEEN 1 PRECEDING AND 1 FOLLOWING EXCLUDE CURRENT ROW) FROM %s e', ContainsNumerics::class),
            'with a frame excluding the group' => \sprintf('SELECT ROW_NUMBER_OVER(ORDER BY e.integer2 GROUPS BETWEEN 1 PRECEDING AND 1 FOLLOWING EXCLUDE GROUP) FROM %s e', ContainsNumerics::class),
            'with a frame excluding ties' => \sprintf('SELECT ROW_NUMBER_OVER(ORDER BY e.integer2 RANGE BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW EXCLUDE TIES) FROM %s e', ContainsNumerics::class),
            'with a frame excluding no others' => \sprintf('SELECT ROW_NUMBER_OVER(ORDER BY e.integer2 ROWS UNBOUNDED PRECEDING EXCLUDE NO OTHERS) FROM %s e', ContainsNumerics::class),
            'with lowercase frame keywords' => \sprintf('SELECT ROW_NUMBER_OVER(ORDER BY e.integer2 rows between unbounded preceding and current row exclude group) FROM %s e', ContainsNumerics::class),
            'next to an entity and a result variable' => \sprintf('SELECT e, ROW_NUMBER_OVER(ORDER BY e.integer2) AS rn FROM %s e ORDER BY rn', ContainsNumerics::class),
=======
            'next to a field and a result variable' => \sprintf('SELECT e.id, ROW_NUMBER_OVER(ORDER BY e.integer2) AS rn FROM %s e ORDER BY rn', ContainsNumerics::class),
>>>>>>> origin/feat/window-value-functions
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

    #[DataProvider('provideMalformedInputs')]
    #[Test]
    public function throws_exception_for_malformed_input(string $dqlFunctionCall): void
    {
        $this->expectException(QueryException::class);

        $dql = \sprintf('SELECT %s FROM %s e', $dqlFunctionCall, ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideMalformedInputs(): array
    {
        return [
            'frame BETWEEN without AND' => ['ROW_NUMBER_OVER(ORDER BY e.integer2 ROWS BETWEEN UNBOUNDED PRECEDING CURRENT ROW)'],
            'unknown frame bound' => ['ROW_NUMBER_OVER(ORDER BY e.integer2 ROWS BETWEEN UNBOUNDED PRECEDING AND SOMEWHERE FOLLOWING)'],
            'frame offset without a direction' => ['ROW_NUMBER_OVER(ORDER BY e.integer2 ROWS 2)'],
            'UNBOUNDED without a direction' => ['ROW_NUMBER_OVER(ORDER BY e.integer2 ROWS UNBOUNDED CURRENT ROW)'],
            'CURRENT without ROW' => ['ROW_NUMBER_OVER(ORDER BY e.integer2 ROWS CURRENT)'],
            'field as a frame offset' => ['ROW_NUMBER_OVER(ORDER BY e.integer2 ROWS e.integer1 PRECEDING)'],
            'unknown frame exclusion' => ['ROW_NUMBER_OVER(ORDER BY e.integer2 ROWS UNBOUNDED PRECEDING EXCLUDE EVERYTHING)'],
            'EXCLUDE NO without OTHERS' => ['ROW_NUMBER_OVER(ORDER BY e.integer2 ROWS UNBOUNDED PRECEDING EXCLUDE NO)'],
            'EXCLUDE CURRENT without ROW' => ['ROW_NUMBER_OVER(ORDER BY e.integer2 ROWS UNBOUNDED PRECEDING EXCLUDE CURRENT)'],
            'frame before ORDER BY' => ['ROW_NUMBER_OVER(ROWS UNBOUNDED PRECEDING ORDER BY e.integer2)'],
        ];
    }
}
