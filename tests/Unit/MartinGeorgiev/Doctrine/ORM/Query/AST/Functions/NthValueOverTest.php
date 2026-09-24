<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NthValueOver;
use PHPUnit\Framework\Attributes\Test;

final class NthValueOverTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'NTH_VALUE_OVER' => NthValueOver::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'without a window specification' => 'SELECT nth_value(c0_.integer1, 2) OVER () AS sclr_0 FROM ContainsNumerics c0_',
            'with PARTITION BY' => 'SELECT nth_value(c0_.integer1, 2) OVER (PARTITION BY c0_.bigint1) AS sclr_0 FROM ContainsNumerics c0_',
            'with ORDER BY' => 'SELECT nth_value(c0_.integer1, 2) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with PARTITION BY and ORDER BY' => 'SELECT nth_value(c0_.integer1, 2) OVER (PARTITION BY c0_.bigint1 ORDER BY c0_.integer2 DESC) AS sclr_0 FROM ContainsNumerics c0_',
            'with a string literal as the value' => "SELECT nth_value('second', 2) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_",
            'with a function call as the value' => 'SELECT nth_value(ABS(c0_.integer1), 2) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with an entity field as the row number' => 'SELECT nth_value(c0_.integer1, c0_.integer2) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with parameters' => 'SELECT nth_value(?, ?) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'without a window specification' => \sprintf('SELECT NTH_VALUE_OVER(e.integer1, 2) FROM %s e', ContainsNumerics::class),
            'with PARTITION BY' => \sprintf('SELECT NTH_VALUE_OVER(e.integer1, 2, PARTITION BY e.bigint1) FROM %s e', ContainsNumerics::class),
            'with ORDER BY' => \sprintf('SELECT NTH_VALUE_OVER(e.integer1, 2, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with PARTITION BY and ORDER BY' => \sprintf('SELECT NTH_VALUE_OVER(e.integer1, 2, PARTITION BY e.bigint1 ORDER BY e.integer2 DESC) FROM %s e', ContainsNumerics::class),
            'with a string literal as the value' => \sprintf("SELECT NTH_VALUE_OVER('second', 2, ORDER BY e.integer2) FROM %s e", ContainsNumerics::class),
            'with a function call as the value' => \sprintf('SELECT NTH_VALUE_OVER(ABS(e.integer1), 2, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with an entity field as the row number' => \sprintf('SELECT NTH_VALUE_OVER(e.integer1, e.integer2, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with parameters' => \sprintf('SELECT NTH_VALUE_OVER(:value, :n, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
        ];
    }

    #[Test]
    public function throws_exception_for_missing_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('nth_value() requires exactly 2 arguments');

        $dql = \sprintf('SELECT NTH_VALUE_OVER(e.integer1, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_window_specification_without_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('nth_value() requires exactly 2 arguments');

        $dql = \sprintf('SELECT NTH_VALUE_OVER(ORDER BY e.integer2) FROM %s e', ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('nth_value() requires exactly 2 arguments');

        $dql = \sprintf('SELECT NTH_VALUE_OVER(e.integer1, 2, 3, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_window_specification_without_a_separating_comma(): void
    {
        $this->expectException(QueryException::class);

        $dql = \sprintf('SELECT NTH_VALUE_OVER(e.integer1, 2 ORDER BY e.integer2) FROM %s e', ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
