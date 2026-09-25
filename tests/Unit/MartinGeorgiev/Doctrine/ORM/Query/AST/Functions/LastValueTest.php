<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\LastValue;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Over;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class LastValueTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LAST_VALUE' => LastValue::class,
            'OVER' => Over::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'reads a field' => 'SELECT last_value(c0_.integer1) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'reads a field within a partition' => 'SELECT last_value(c0_.integer1) OVER (PARTITION BY c0_.bigint1 ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'reads a field across the whole frame' => 'SELECT last_value(c0_.integer1) OVER (PARTITION BY c0_.bigint1 ORDER BY c0_.integer2 ASC ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING) AS sclr_0 FROM ContainsNumerics c0_',
            'reads a numeric literal' => 'SELECT last_value(5) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'reads a string literal' => "SELECT last_value('value') OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_",
            'reads a parameter' => 'SELECT last_value(?) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'reads an arithmetic expression' => 'SELECT last_value(c0_.integer1 + c0_.integer2) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'reads a field' => \sprintf('SELECT OVER(LAST_VALUE(e.integer1), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'reads a field within a partition' => \sprintf('SELECT OVER(LAST_VALUE(e.integer1), PARTITION BY e.bigint1 ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'reads a field across the whole frame' => \sprintf('SELECT OVER(LAST_VALUE(e.integer1), PARTITION BY e.bigint1 ORDER BY e.integer2 ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING) FROM %s e', ContainsNumerics::class),
            'reads a numeric literal' => \sprintf('SELECT OVER(LAST_VALUE(5), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'reads a string literal' => \sprintf("SELECT OVER(LAST_VALUE('value'), ORDER BY e.integer2) FROM %s e", ContainsNumerics::class),
            'reads a parameter' => \sprintf('SELECT OVER(LAST_VALUE(:value), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'reads an arithmetic expression' => \sprintf('SELECT OVER(LAST_VALUE(e.integer1 + e.integer2), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
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
            'empty argument list' => ['OVER(LAST_VALUE(), ORDER BY e.integer2)'],
            'too many arguments' => ['OVER(LAST_VALUE(e.integer1, 1), ORDER BY e.integer2)'],
        ];
    }
}
