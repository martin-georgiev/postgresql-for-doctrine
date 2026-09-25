<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use Fixtures\MartinGeorgiev\Doctrine\Function\TestWindowFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAgg;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\ParserException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Filter;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Over;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class OverTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_AGG' => ArrayAgg::class,
            'FILTER' => Filter::class,
            'OVER' => Over::class,
            'TEST_WINDOW' => TestWindowFunction::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'windows COUNT without a window specification' => 'SELECT COUNT(c0_.id) OVER () AS sclr_0 FROM ContainsNumerics c0_',
            'windows SUM with PARTITION BY' => 'SELECT SUM(c0_.decimal1) OVER (PARTITION BY c0_.integer1) AS sclr_0 FROM ContainsNumerics c0_',
            'windows AVG with ORDER BY' => 'SELECT AVG(c0_.decimal1) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'windows MIN with PARTITION BY and ORDER BY' => 'SELECT MIN(c0_.integer1) OVER (PARTITION BY c0_.bigint1 ORDER BY c0_.integer2 DESC) AS sclr_0 FROM ContainsNumerics c0_',
            'windows MAX with multiple PARTITION BY expressions' => 'SELECT MAX(c0_.integer1) OVER (PARTITION BY c0_.bigint1, c0_.bigint2) AS sclr_0 FROM ContainsNumerics c0_',
            'windows a running total' => 'SELECT SUM(c0_.decimal1) OVER (PARTITION BY c0_.integer1 ORDER BY c0_.integer2 ASC ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS sclr_0 FROM ContainsNumerics c0_',
            'windows a moving average with a parameter offset' => 'SELECT AVG(c0_.decimal1) OVER (ORDER BY c0_.integer2 ASC ROWS BETWEEN ? PRECEDING AND CURRENT ROW) AS sclr_0 FROM ContainsNumerics c0_',
            'windows with only a frame' => 'SELECT SUM(c0_.decimal1) OVER (ROWS BETWEEN 1 PRECEDING AND 1 FOLLOWING EXCLUDE CURRENT ROW) AS sclr_0 FROM ContainsNumerics c0_',
            'windows a library aggregate' => 'SELECT array_agg(c0_.integer1) OVER (PARTITION BY c0_.integer2) AS sclr_0 FROM ContainsNumerics c0_',
            'windows a filtered aggregate' => 'SELECT SUM(c0_.decimal1) FILTER (WHERE c0_.integer1 > 0) OVER (PARTITION BY c0_.integer2) AS sclr_0 FROM ContainsNumerics c0_',
            'next to a field and a result variable' => 'SELECT c0_.id AS id_0, SUM(c0_.decimal1) OVER (PARTITION BY c0_.integer1) AS sclr_1 FROM ContainsNumerics c0_ ORDER BY sclr_1 ASC',
            'with PARTITION BY' => 'SELECT COUNT(c0_.id) OVER (PARTITION BY c0_.integer1) AS sclr_0 FROM ContainsNumerics c0_',
            'with ORDER BY' => 'SELECT COUNT(c0_.id) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with PARTITION BY and ORDER BY' => 'SELECT COUNT(c0_.id) OVER (PARTITION BY c0_.integer1 ORDER BY c0_.integer2 DESC) AS sclr_0 FROM ContainsNumerics c0_',
            'with multiple PARTITION BY expressions' => 'SELECT COUNT(c0_.id) OVER (PARTITION BY c0_.integer1, c0_.bigint1 ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with multiple ORDER BY items' => 'SELECT COUNT(c0_.id) OVER (ORDER BY c0_.integer1 ASC, c0_.integer2 DESC) AS sclr_0 FROM ContainsNumerics c0_',
            'with an arithmetic PARTITION BY expression' => 'SELECT COUNT(c0_.id) OVER (PARTITION BY c0_.integer1 + 1) AS sclr_0 FROM ContainsNumerics c0_',
            'with a function in PARTITION BY' => 'SELECT COUNT(c0_.id) OVER (PARTITION BY ABS(c0_.integer1)) AS sclr_0 FROM ContainsNumerics c0_',
            'with a lowercase PARTITION keyword' => 'SELECT COUNT(c0_.id) OVER (PARTITION BY c0_.integer1) AS sclr_0 FROM ContainsNumerics c0_',
            'with a ROWS frame start' => 'SELECT COUNT(c0_.id) OVER (ORDER BY c0_.integer2 ASC ROWS UNBOUNDED PRECEDING) AS sclr_0 FROM ContainsNumerics c0_',
            'with a ROWS frame between bounds' => 'SELECT COUNT(c0_.id) OVER (ORDER BY c0_.integer2 ASC ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS sclr_0 FROM ContainsNumerics c0_',
            'with a RANGE frame' => 'SELECT COUNT(c0_.id) OVER (ORDER BY c0_.integer2 ASC RANGE BETWEEN CURRENT ROW AND UNBOUNDED FOLLOWING) AS sclr_0 FROM ContainsNumerics c0_',
            'with a GROUPS frame' => 'SELECT COUNT(c0_.id) OVER (ORDER BY c0_.integer2 ASC GROUPS BETWEEN 1 PRECEDING AND 1 FOLLOWING) AS sclr_0 FROM ContainsNumerics c0_',
            'with an integer offset frame start' => 'SELECT COUNT(c0_.id) OVER (ORDER BY c0_.integer2 ASC ROWS 2 PRECEDING) AS sclr_0 FROM ContainsNumerics c0_',
            'with a CURRENT ROW frame start' => 'SELECT COUNT(c0_.id) OVER (ORDER BY c0_.integer2 ASC ROWS CURRENT ROW) AS sclr_0 FROM ContainsNumerics c0_',
            'with parameter frame offsets' => 'SELECT COUNT(c0_.id) OVER (ORDER BY c0_.integer2 ASC ROWS BETWEEN ? PRECEDING AND ? FOLLOWING) AS sclr_0 FROM ContainsNumerics c0_',
            'with an interval frame offset' => "SELECT COUNT(c0_.id) OVER (ORDER BY c0_.integer2 ASC RANGE BETWEEN '1 day' PRECEDING AND CURRENT ROW) AS sclr_0 FROM ContainsNumerics c0_",
            'with a decimal frame offset' => 'SELECT COUNT(c0_.id) OVER (ORDER BY c0_.integer2 ASC RANGE BETWEEN 0.5 PRECEDING AND 0.5 FOLLOWING) AS sclr_0 FROM ContainsNumerics c0_',
            'with a frame and no ORDER BY' => 'SELECT COUNT(c0_.id) OVER (ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING) AS sclr_0 FROM ContainsNumerics c0_',
            'with PARTITION BY and a frame' => 'SELECT COUNT(c0_.id) OVER (PARTITION BY c0_.integer1 ROWS UNBOUNDED PRECEDING) AS sclr_0 FROM ContainsNumerics c0_',
            'with a frame excluding the current row' => 'SELECT COUNT(c0_.id) OVER (ORDER BY c0_.integer2 ASC ROWS BETWEEN 1 PRECEDING AND 1 FOLLOWING EXCLUDE CURRENT ROW) AS sclr_0 FROM ContainsNumerics c0_',
            'with a frame excluding the group' => 'SELECT COUNT(c0_.id) OVER (ORDER BY c0_.integer2 ASC GROUPS BETWEEN 1 PRECEDING AND 1 FOLLOWING EXCLUDE GROUP) AS sclr_0 FROM ContainsNumerics c0_',
            'with a frame excluding ties' => 'SELECT COUNT(c0_.id) OVER (ORDER BY c0_.integer2 ASC RANGE BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW EXCLUDE TIES) AS sclr_0 FROM ContainsNumerics c0_',
            'with a frame excluding no others' => 'SELECT COUNT(c0_.id) OVER (ORDER BY c0_.integer2 ASC ROWS UNBOUNDED PRECEDING EXCLUDE NO OTHERS) AS sclr_0 FROM ContainsNumerics c0_',
            'with lowercase frame keywords' => 'SELECT COUNT(c0_.id) OVER (ORDER BY c0_.integer2 ASC ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW EXCLUDE GROUP) AS sclr_0 FROM ContainsNumerics c0_',
            'windows a window function' => 'SELECT test_window() OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'windows COUNT without a window specification' => \sprintf('SELECT OVER(COUNT(e.id)) FROM %s e', ContainsNumerics::class),
            'windows SUM with PARTITION BY' => \sprintf('SELECT OVER(SUM(e.decimal1), PARTITION BY e.integer1) FROM %s e', ContainsNumerics::class),
            'windows AVG with ORDER BY' => \sprintf('SELECT OVER(AVG(e.decimal1), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'windows MIN with PARTITION BY and ORDER BY' => \sprintf('SELECT OVER(MIN(e.integer1), PARTITION BY e.bigint1 ORDER BY e.integer2 DESC) FROM %s e', ContainsNumerics::class),
            'windows MAX with multiple PARTITION BY expressions' => \sprintf('SELECT OVER(MAX(e.integer1), PARTITION BY e.bigint1, e.bigint2) FROM %s e', ContainsNumerics::class),
            'windows a running total' => \sprintf('SELECT OVER(SUM(e.decimal1), PARTITION BY e.integer1 ORDER BY e.integer2 ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) FROM %s e', ContainsNumerics::class),
            'windows a moving average with a parameter offset' => \sprintf('SELECT OVER(AVG(e.decimal1), ORDER BY e.integer2 ROWS BETWEEN :before PRECEDING AND CURRENT ROW) FROM %s e', ContainsNumerics::class),
            'windows with only a frame' => \sprintf('SELECT OVER(SUM(e.decimal1), ROWS BETWEEN 1 PRECEDING AND 1 FOLLOWING EXCLUDE CURRENT ROW) FROM %s e', ContainsNumerics::class),
            'windows a library aggregate' => \sprintf('SELECT OVER(ARRAY_AGG(e.integer1), PARTITION BY e.integer2) FROM %s e', ContainsNumerics::class),
            'windows a filtered aggregate' => \sprintf('SELECT OVER(FILTER(SUM(e.decimal1), WHERE e.integer1 > 0), PARTITION BY e.integer2) FROM %s e', ContainsNumerics::class),
            'next to a field and a result variable' => \sprintf('SELECT e.id, OVER(SUM(e.decimal1), PARTITION BY e.integer1) AS total FROM %s e ORDER BY total', ContainsNumerics::class),
            'with PARTITION BY' => \sprintf('SELECT OVER(COUNT(e.id), PARTITION BY e.integer1) FROM %s e', ContainsNumerics::class),
            'with ORDER BY' => \sprintf('SELECT OVER(COUNT(e.id), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with PARTITION BY and ORDER BY' => \sprintf('SELECT OVER(COUNT(e.id), PARTITION BY e.integer1 ORDER BY e.integer2 DESC) FROM %s e', ContainsNumerics::class),
            'with multiple PARTITION BY expressions' => \sprintf('SELECT OVER(COUNT(e.id), PARTITION BY e.integer1, e.bigint1 ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with multiple ORDER BY items' => \sprintf('SELECT OVER(COUNT(e.id), ORDER BY e.integer1 ASC, e.integer2 DESC) FROM %s e', ContainsNumerics::class),
            'with an arithmetic PARTITION BY expression' => \sprintf('SELECT OVER(COUNT(e.id), PARTITION BY e.integer1 + 1) FROM %s e', ContainsNumerics::class),
            'with a function in PARTITION BY' => \sprintf('SELECT OVER(COUNT(e.id), PARTITION BY ABS(e.integer1)) FROM %s e', ContainsNumerics::class),
            'with a lowercase PARTITION keyword' => \sprintf('SELECT OVER(COUNT(e.id), partition by e.integer1) FROM %s e', ContainsNumerics::class),
            'with a ROWS frame start' => \sprintf('SELECT OVER(COUNT(e.id), ORDER BY e.integer2 ROWS UNBOUNDED PRECEDING) FROM %s e', ContainsNumerics::class),
            'with a ROWS frame between bounds' => \sprintf('SELECT OVER(COUNT(e.id), ORDER BY e.integer2 ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) FROM %s e', ContainsNumerics::class),
            'with a RANGE frame' => \sprintf('SELECT OVER(COUNT(e.id), ORDER BY e.integer2 RANGE BETWEEN CURRENT ROW AND UNBOUNDED FOLLOWING) FROM %s e', ContainsNumerics::class),
            'with a GROUPS frame' => \sprintf('SELECT OVER(COUNT(e.id), ORDER BY e.integer2 GROUPS BETWEEN 1 PRECEDING AND 1 FOLLOWING) FROM %s e', ContainsNumerics::class),
            'with an integer offset frame start' => \sprintf('SELECT OVER(COUNT(e.id), ORDER BY e.integer2 ROWS 2 PRECEDING) FROM %s e', ContainsNumerics::class),
            'with a CURRENT ROW frame start' => \sprintf('SELECT OVER(COUNT(e.id), ORDER BY e.integer2 ROWS CURRENT ROW) FROM %s e', ContainsNumerics::class),
            'with parameter frame offsets' => \sprintf('SELECT OVER(COUNT(e.id), ORDER BY e.integer2 ROWS BETWEEN :before PRECEDING AND :after FOLLOWING) FROM %s e', ContainsNumerics::class),
            'with an interval frame offset' => \sprintf("SELECT OVER(COUNT(e.id), ORDER BY e.integer2 RANGE BETWEEN '1 day' PRECEDING AND CURRENT ROW) FROM %s e", ContainsNumerics::class),
            'with a decimal frame offset' => \sprintf('SELECT OVER(COUNT(e.id), ORDER BY e.integer2 RANGE BETWEEN 0.5 PRECEDING AND 0.5 FOLLOWING) FROM %s e', ContainsNumerics::class),
            'with a frame and no ORDER BY' => \sprintf('SELECT OVER(COUNT(e.id), ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING) FROM %s e', ContainsNumerics::class),
            'with PARTITION BY and a frame' => \sprintf('SELECT OVER(COUNT(e.id), PARTITION BY e.integer1 ROWS UNBOUNDED PRECEDING) FROM %s e', ContainsNumerics::class),
            'with a frame excluding the current row' => \sprintf('SELECT OVER(COUNT(e.id), ORDER BY e.integer2 ROWS BETWEEN 1 PRECEDING AND 1 FOLLOWING EXCLUDE CURRENT ROW) FROM %s e', ContainsNumerics::class),
            'with a frame excluding the group' => \sprintf('SELECT OVER(COUNT(e.id), ORDER BY e.integer2 GROUPS BETWEEN 1 PRECEDING AND 1 FOLLOWING EXCLUDE GROUP) FROM %s e', ContainsNumerics::class),
            'with a frame excluding ties' => \sprintf('SELECT OVER(COUNT(e.id), ORDER BY e.integer2 RANGE BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW EXCLUDE TIES) FROM %s e', ContainsNumerics::class),
            'with a frame excluding no others' => \sprintf('SELECT OVER(COUNT(e.id), ORDER BY e.integer2 ROWS UNBOUNDED PRECEDING EXCLUDE NO OTHERS) FROM %s e', ContainsNumerics::class),
            'with lowercase frame keywords' => \sprintf('SELECT OVER(COUNT(e.id), ORDER BY e.integer2 rows between unbounded preceding and current row exclude group) FROM %s e', ContainsNumerics::class),
            'windows a window function' => \sprintf('SELECT OVER(TEST_WINDOW(), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
        ];
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
            'missing aggregate' => ['OVER()'],
            'field instead of an aggregate' => ['OVER(e.integer1, PARTITION BY e.integer2)'],
            'literal instead of an aggregate' => ['OVER(5, PARTITION BY e.integer2)'],
            'unregistered function instead of an aggregate' => ['OVER(UNKNOWN_AGG(e.integer1))'],
            'comma without a window specification' => ['OVER(COUNT(e.id), )'],
            'expression instead of a window specification' => ['OVER(COUNT(e.id), e.integer1)'],
            'window specification without a separating comma' => ['OVER(COUNT(e.id) ORDER BY e.integer2)'],
            'malformed frame' => ['OVER(SUM(e.decimal1), ORDER BY e.integer2 ROWS BETWEEN 1 PRECEDING)'],
            'frame BETWEEN without AND' => ['OVER(COUNT(e.id), ORDER BY e.integer2 ROWS BETWEEN UNBOUNDED PRECEDING CURRENT ROW)'],
            'unknown frame bound' => ['OVER(COUNT(e.id), ORDER BY e.integer2 ROWS BETWEEN UNBOUNDED PRECEDING AND SOMEWHERE FOLLOWING)'],
            'frame offset without a direction' => ['OVER(COUNT(e.id), ORDER BY e.integer2 ROWS 2)'],
            'UNBOUNDED without a direction' => ['OVER(COUNT(e.id), ORDER BY e.integer2 ROWS UNBOUNDED CURRENT ROW)'],
            'CURRENT without ROW' => ['OVER(COUNT(e.id), ORDER BY e.integer2 ROWS CURRENT)'],
            'field as a frame offset' => ['OVER(COUNT(e.id), ORDER BY e.integer2 ROWS e.integer1 PRECEDING)'],
            'unknown frame exclusion' => ['OVER(COUNT(e.id), ORDER BY e.integer2 ROWS UNBOUNDED PRECEDING EXCLUDE EVERYTHING)'],
            'EXCLUDE NO without OTHERS' => ['OVER(COUNT(e.id), ORDER BY e.integer2 ROWS UNBOUNDED PRECEDING EXCLUDE NO)'],
            'EXCLUDE CURRENT without ROW' => ['OVER(COUNT(e.id), ORDER BY e.integer2 ROWS UNBOUNDED PRECEDING EXCLUDE CURRENT)'],
            'frame before ORDER BY' => ['OVER(COUNT(e.id), ROWS UNBOUNDED PRECEDING ORDER BY e.integer2)'],
            'PARTITION without BY' => ['OVER(COUNT(e.id), PARTITION e.integer1)'],
            'ORDER BY before PARTITION BY' => ['OVER(COUNT(e.id), ORDER BY e.integer2 PARTITION BY e.integer1)'],
        ];
    }

    #[DataProvider('provideNonWindowableArguments')]
    #[Test]
    public function throws_exception_when_the_argument_is_neither_an_aggregate_nor_a_window_function(string $dqlFunctionCall): void
    {
        $this->expectException(ParserException::class);

        $dql = \sprintf('SELECT %s FROM %s e', $dqlFunctionCall, ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideNonWindowableArguments(): array
    {
        return [
            'scalar function' => ['OVER(ABS(e.integer1), PARTITION BY e.integer2)'],
            'nested OVER' => ['OVER(OVER(COUNT(e.id)), PARTITION BY e.integer2)'],
        ];
    }
}
