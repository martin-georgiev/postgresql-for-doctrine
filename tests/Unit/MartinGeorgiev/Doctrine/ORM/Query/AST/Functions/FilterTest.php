<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAgg;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\ParserException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Filter;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PercentileCont;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class FilterTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_AGG' => ArrayAgg::class,
            'FILTER' => Filter::class,
            'PERCENTILE_CONT' => PercentileCont::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'filters COUNT' => 'SELECT COUNT(c0_.id) FILTER (WHERE c0_.integer1 > 5) AS sclr_0 FROM ContainsNumerics c0_',
            'filters COUNT DISTINCT' => 'SELECT COUNT(DISTINCT c0_.integer1) FILTER (WHERE c0_.integer2 = 20) AS sclr_0 FROM ContainsNumerics c0_',
            'filters SUM' => 'SELECT SUM(c0_.decimal1) FILTER (WHERE c0_.integer2 = 20) AS sclr_0 FROM ContainsNumerics c0_',
            'filters AVG' => 'SELECT AVG(c0_.decimal1) FILTER (WHERE c0_.integer1 < c0_.integer2) AS sclr_0 FROM ContainsNumerics c0_',
            'filters MIN' => 'SELECT MIN(c0_.integer1) FILTER (WHERE c0_.integer1 IS NOT NULL) AS sclr_0 FROM ContainsNumerics c0_',
            'filters MAX' => 'SELECT MAX(c0_.integer1) FILTER (WHERE c0_.integer1 IN (1, 2)) AS sclr_0 FROM ContainsNumerics c0_',
            'filters a custom aggregate with ORDER BY' => 'SELECT array_agg(c0_.integer1 ORDER BY c0_.integer2 DESC) FILTER (WHERE c0_.decimal1 IS NOT NULL) AS sclr_0 FROM ContainsNumerics c0_',
            'filters an ordered-set aggregate' => 'SELECT percentile_cont(0.5) WITHIN GROUP (ORDER BY c0_.decimal1 ASC) FILTER (WHERE c0_.integer1 > 5) AS sclr_0 FROM ContainsNumerics c0_',
            'filters with a compound condition' => 'SELECT COUNT(c0_.id) FILTER (WHERE c0_.integer1 > 5 AND (c0_.integer2 < 10 OR c0_.decimal1 IS NULL)) AS sclr_0 FROM ContainsNumerics c0_',
            'filters with a parameter' => 'SELECT COUNT(c0_.id) FILTER (WHERE c0_.integer1 > ?) AS sclr_0 FROM ContainsNumerics c0_',
            'filters in HAVING' => 'SELECT c0_.integer1 AS integer1_0 FROM ContainsNumerics c0_ GROUP BY c0_.integer1 HAVING COUNT(c0_.id) FILTER (WHERE c0_.integer2 > 5) > 1',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'filters COUNT' => \sprintf('SELECT FILTER(COUNT(e.id), WHERE e.integer1 > 5) FROM %s e', ContainsNumerics::class),
            'filters COUNT DISTINCT' => \sprintf('SELECT FILTER(COUNT(DISTINCT e.integer1), WHERE e.integer2 = 20) FROM %s e', ContainsNumerics::class),
            'filters SUM' => \sprintf('SELECT FILTER(SUM(e.decimal1), WHERE e.integer2 = 20) FROM %s e', ContainsNumerics::class),
            'filters AVG' => \sprintf('SELECT FILTER(AVG(e.decimal1), WHERE e.integer1 < e.integer2) FROM %s e', ContainsNumerics::class),
            'filters MIN' => \sprintf('SELECT FILTER(MIN(e.integer1), WHERE e.integer1 IS NOT NULL) FROM %s e', ContainsNumerics::class),
            'filters MAX' => \sprintf('SELECT FILTER(MAX(e.integer1), WHERE e.integer1 IN (1, 2)) FROM %s e', ContainsNumerics::class),
            'filters a custom aggregate with ORDER BY' => \sprintf('SELECT FILTER(ARRAY_AGG(e.integer1 ORDER BY e.integer2 DESC), WHERE e.decimal1 IS NOT NULL) FROM %s e', ContainsNumerics::class),
            'filters an ordered-set aggregate' => \sprintf('SELECT FILTER(PERCENTILE_CONT(0.5 WITHIN GROUP ORDER BY e.decimal1), WHERE e.integer1 > 5) FROM %s e', ContainsNumerics::class),
            'filters with a compound condition' => \sprintf('SELECT FILTER(COUNT(e.id), WHERE e.integer1 > 5 AND (e.integer2 < 10 OR e.decimal1 IS NULL)) FROM %s e', ContainsNumerics::class),
            'filters with a parameter' => \sprintf('SELECT FILTER(COUNT(e.id), WHERE e.integer1 > :threshold) FROM %s e', ContainsNumerics::class),
            'filters in HAVING' => \sprintf('SELECT e.integer1 FROM %s e GROUP BY e.integer1 HAVING FILTER(COUNT(e.id), WHERE e.integer2 > 5) > 1', ContainsNumerics::class),
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
            'missing WHERE' => ['FILTER(COUNT(e.id), e.integer1 > 5)'],
            'missing condition' => ['FILTER(COUNT(e.id), WHERE)'],
            'missing second argument' => ['FILTER(COUNT(e.id))'],
            'field instead of an aggregate' => ['FILTER(e.integer1, WHERE e.integer1 > 5)'],
            'literal instead of an aggregate' => ['FILTER(5, WHERE e.integer1 > 5)'],
            'unregistered function instead of an aggregate' => ['FILTER(UNKNOWN_AGG(e.integer1), WHERE e.integer1 > 5)'],
            'extra argument' => ['FILTER(COUNT(e.id), WHERE e.integer1 > 5, e.integer2)'],
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
            'scalar function' => ['FILTER(ABS(e.integer1), WHERE e.integer1 > 5)'],
            'nested FILTER' => ['FILTER(FILTER(COUNT(e.id), WHERE e.integer1 > 5), WHERE e.integer2 > 5)'],
        ];
    }
}
