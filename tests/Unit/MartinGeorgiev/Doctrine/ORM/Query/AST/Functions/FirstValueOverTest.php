<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\FirstValueOver;
use PHPUnit\Framework\Attributes\Test;

final class FirstValueOverTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'FIRST_VALUE_OVER' => FirstValueOver::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'without a window specification' => 'SELECT first_value(c0_.integer1) OVER () AS sclr_0 FROM ContainsNumerics c0_',
            'with PARTITION BY' => 'SELECT first_value(c0_.integer1) OVER (PARTITION BY c0_.bigint1) AS sclr_0 FROM ContainsNumerics c0_',
            'with ORDER BY' => 'SELECT first_value(c0_.integer1) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with PARTITION BY and ORDER BY' => 'SELECT first_value(c0_.integer1) OVER (PARTITION BY c0_.bigint1 ORDER BY c0_.integer2 DESC) AS sclr_0 FROM ContainsNumerics c0_',
            'with a numeric literal as the value' => 'SELECT first_value(5) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with a string literal as the value' => "SELECT first_value('first') OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_",
            'with a function call as the value' => 'SELECT first_value(ABS(c0_.integer1)) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with a parameter as the value' => 'SELECT first_value(?) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'without a window specification' => \sprintf('SELECT FIRST_VALUE_OVER(e.integer1) FROM %s e', ContainsNumerics::class),
            'with PARTITION BY' => \sprintf('SELECT FIRST_VALUE_OVER(e.integer1, PARTITION BY e.bigint1) FROM %s e', ContainsNumerics::class),
            'with ORDER BY' => \sprintf('SELECT FIRST_VALUE_OVER(e.integer1, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with PARTITION BY and ORDER BY' => \sprintf('SELECT FIRST_VALUE_OVER(e.integer1, PARTITION BY e.bigint1 ORDER BY e.integer2 DESC) FROM %s e', ContainsNumerics::class),
            'with a numeric literal as the value' => \sprintf('SELECT FIRST_VALUE_OVER(5, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with a string literal as the value' => \sprintf("SELECT FIRST_VALUE_OVER('first', ORDER BY e.integer2) FROM %s e", ContainsNumerics::class),
            'with a function call as the value' => \sprintf('SELECT FIRST_VALUE_OVER(ABS(e.integer1), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with a parameter as the value' => \sprintf('SELECT FIRST_VALUE_OVER(:value, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
        ];
    }

    #[Test]
    public function throws_exception_for_missing_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('first_value() requires exactly 1 argument');

        $dql = \sprintf('SELECT FIRST_VALUE_OVER() FROM %s e', ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_window_specification_without_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('first_value() requires exactly 1 argument');

        $dql = \sprintf('SELECT FIRST_VALUE_OVER(ORDER BY e.integer2) FROM %s e', ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('first_value() requires exactly 1 argument');

        $dql = \sprintf('SELECT FIRST_VALUE_OVER(e.integer1, 2, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_window_specification_without_a_separating_comma(): void
    {
        $this->expectException(QueryException::class);

        $dql = \sprintf('SELECT FIRST_VALUE_OVER(e.integer1 ORDER BY e.integer2) FROM %s e', ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
