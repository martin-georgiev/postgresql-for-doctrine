<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AggregateFilter;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AggregateOver;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAgg;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\ParserException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RowNumberOver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class AggregateOverTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_AGG' => ArrayAgg::class,
            'FILTER' => AggregateFilter::class,
            'OVER' => AggregateOver::class,
            'ROW_NUMBER_OVER' => RowNumberOver::class,
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
            'next to an entity and a result variable' => 'SELECT c0_.id AS id_0, c0_.integer1 AS integer1_1, c0_.integer2 AS integer2_2, c0_.bigint1 AS bigint1_3, c0_.bigint2 AS bigint2_4, c0_.decimal1 AS decimal1_5, c0_.decimal2 AS decimal2_6, SUM(c0_.decimal1) OVER (PARTITION BY c0_.integer1) AS sclr_7 FROM ContainsNumerics c0_ ORDER BY sclr_7 ASC',
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
            'next to an entity and a result variable' => \sprintf('SELECT e, OVER(SUM(e.decimal1), PARTITION BY e.integer1) AS total FROM %s e ORDER BY total', ContainsNumerics::class),
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
        ];
    }

    #[DataProvider('provideNonAggregateArguments')]
    #[Test]
    public function throws_exception_when_the_argument_is_not_an_aggregate(string $dqlFunctionCall): void
    {
        $this->expectException(ParserException::class);

        $dql = \sprintf('SELECT %s FROM %s e', $dqlFunctionCall, ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideNonAggregateArguments(): array
    {
        return [
            'scalar function' => ['OVER(ABS(e.integer1), PARTITION BY e.integer2)'],
            'window function' => ['OVER(ROW_NUMBER_OVER(ORDER BY e.integer1), PARTITION BY e.integer2)'],
        ];
    }
}
