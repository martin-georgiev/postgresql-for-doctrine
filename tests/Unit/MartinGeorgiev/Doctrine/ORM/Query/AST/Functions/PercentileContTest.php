<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PercentileCont;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class PercentileContTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'PERCENTILE_CONT' => PercentileCont::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'orders ascending by default' => 'SELECT percentile_cont(0.5) WITHIN GROUP (ORDER BY c0_.decimal1 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'orders descending' => 'SELECT percentile_cont(0.9) WITHIN GROUP (ORDER BY c0_.decimal1 DESC) AS sclr_0 FROM ContainsNumerics c0_',
            'takes the fraction as a parameter' => 'SELECT percentile_cont(?) WITHIN GROUP (ORDER BY c0_.decimal1 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'matches WITHIN case-insensitively' => 'SELECT percentile_cont(0.5) WITHIN GROUP (ORDER BY c0_.decimal1 ASC) AS sclr_0 FROM ContainsNumerics c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'orders ascending by default' => \sprintf('SELECT PERCENTILE_CONT(0.5 WITHIN GROUP ORDER BY e.decimal1) FROM %s e', ContainsNumerics::class),
            'orders descending' => \sprintf('SELECT PERCENTILE_CONT(0.9 WITHIN GROUP ORDER BY e.decimal1 DESC) FROM %s e', ContainsNumerics::class),
            'takes the fraction as a parameter' => \sprintf('SELECT PERCENTILE_CONT(:fraction WITHIN GROUP ORDER BY e.decimal1) FROM %s e', ContainsNumerics::class),
            'matches WITHIN case-insensitively' => \sprintf('SELECT PERCENTILE_CONT(0.5 within group order by e.decimal1) FROM %s e', ContainsNumerics::class),
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
            'missing WITHIN GROUP' => ['PERCENTILE_CONT(0.5 ORDER BY e.decimal1)'],
            'WITHIN without GROUP' => ['PERCENTILE_CONT(0.5 WITHIN ORDER BY e.decimal1)'],
            'GROUP without ORDER BY' => ['PERCENTILE_CONT(0.5 WITHIN GROUP e.decimal1)'],
            'missing fraction' => ['PERCENTILE_CONT(WITHIN GROUP ORDER BY e.decimal1)'],
            'fraction separated by a comma' => ['PERCENTILE_CONT(0.5, WITHIN GROUP ORDER BY e.decimal1)'],
            'another word in place of WITHIN' => ['PERCENTILE_CONT(0.5 INSIDE GROUP ORDER BY e.decimal1)'],
        ];
    }
}
