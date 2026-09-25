<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NthValue;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Over;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class NthValueTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'NTH_VALUE' => NthValue::class,
            'OVER' => Over::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'reads a field' => 'SELECT nth_value(c0_.integer1, 2) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'reads a field within a partition' => 'SELECT nth_value(c0_.integer1, 2) OVER (PARTITION BY c0_.bigint1 ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'reads a field across the whole frame' => 'SELECT nth_value(c0_.integer1, 2) OVER (ORDER BY c0_.integer2 ASC ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING) AS sclr_0 FROM ContainsNumerics c0_',
            'reads a numeric literal' => 'SELECT nth_value(5, 1) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'reads a string literal' => "SELECT nth_value('value', 1) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_",
            'reads a parameter' => 'SELECT nth_value(?, 1) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with a field position' => 'SELECT nth_value(c0_.integer1, c0_.bigint1) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with an arithmetic position' => 'SELECT nth_value(c0_.integer1, c0_.bigint1 + 1) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with a parameter position' => 'SELECT nth_value(c0_.integer1, ?) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'reads a field' => \sprintf('SELECT OVER(NTH_VALUE(e.integer1, 2), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'reads a field within a partition' => \sprintf('SELECT OVER(NTH_VALUE(e.integer1, 2), PARTITION BY e.bigint1 ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'reads a field across the whole frame' => \sprintf('SELECT OVER(NTH_VALUE(e.integer1, 2), ORDER BY e.integer2 ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING) FROM %s e', ContainsNumerics::class),
            'reads a numeric literal' => \sprintf('SELECT OVER(NTH_VALUE(5, 1), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'reads a string literal' => \sprintf("SELECT OVER(NTH_VALUE('value', 1), ORDER BY e.integer2) FROM %s e", ContainsNumerics::class),
            'reads a parameter' => \sprintf('SELECT OVER(NTH_VALUE(:value, 1), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with a field position' => \sprintf('SELECT OVER(NTH_VALUE(e.integer1, e.bigint1), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with an arithmetic position' => \sprintf('SELECT OVER(NTH_VALUE(e.integer1, e.bigint1 + 1), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with a parameter position' => \sprintf('SELECT OVER(NTH_VALUE(e.integer1, :position), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
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
            'empty argument list' => ['OVER(NTH_VALUE(), ORDER BY e.integer2)'],
            'missing position' => ['OVER(NTH_VALUE(e.integer1), ORDER BY e.integer2)'],
            'too many arguments' => ['OVER(NTH_VALUE(e.integer1, 2, 3), ORDER BY e.integer2)'],
        ];
    }
}
