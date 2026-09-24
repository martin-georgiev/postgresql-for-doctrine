<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\LeadOver;
use PHPUnit\Framework\Attributes\Test;

final class LeadOverTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LEAD_OVER' => LeadOver::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'without a window specification' => 'SELECT lead(c0_.integer1) OVER () AS sclr_0 FROM ContainsNumerics c0_',
            'with PARTITION BY' => 'SELECT lead(c0_.integer1) OVER (PARTITION BY c0_.bigint1) AS sclr_0 FROM ContainsNumerics c0_',
            'with ORDER BY' => 'SELECT lead(c0_.integer1) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with PARTITION BY and ORDER BY' => 'SELECT lead(c0_.integer1) OVER (PARTITION BY c0_.bigint1 ORDER BY c0_.integer2 DESC) AS sclr_0 FROM ContainsNumerics c0_',
            'with an offset' => 'SELECT lead(c0_.integer1, 2) OVER (PARTITION BY c0_.bigint1 ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with an offset and a default' => 'SELECT lead(c0_.integer1, 1, 0) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with a NULL default' => 'SELECT lead(c0_.integer1, 1, NULL) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with an entity field as the default' => 'SELECT lead(c0_.integer1, 1, c0_.integer2) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with string literals as the value and the default' => "SELECT lead('last', 1, 'none') OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_",
            'with a function call as the value' => 'SELECT lead(ABS(c0_.integer1)) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with an arithmetic expression as the value' => 'SELECT lead(c0_.integer1 + 1) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with parameters' => 'SELECT lead(?, ?, ?) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'without a window specification' => \sprintf('SELECT LEAD_OVER(e.integer1) FROM %s e', ContainsNumerics::class),
            'with PARTITION BY' => \sprintf('SELECT LEAD_OVER(e.integer1, PARTITION BY e.bigint1) FROM %s e', ContainsNumerics::class),
            'with ORDER BY' => \sprintf('SELECT LEAD_OVER(e.integer1, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with PARTITION BY and ORDER BY' => \sprintf('SELECT LEAD_OVER(e.integer1, PARTITION BY e.bigint1 ORDER BY e.integer2 DESC) FROM %s e', ContainsNumerics::class),
            'with an offset' => \sprintf('SELECT LEAD_OVER(e.integer1, 2, PARTITION BY e.bigint1 ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with an offset and a default' => \sprintf('SELECT LEAD_OVER(e.integer1, 1, 0, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with a NULL default' => \sprintf('SELECT LEAD_OVER(e.integer1, 1, NULL, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with an entity field as the default' => \sprintf('SELECT LEAD_OVER(e.integer1, 1, e.integer2, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with string literals as the value and the default' => \sprintf("SELECT LEAD_OVER('last', 1, 'none', ORDER BY e.integer2) FROM %s e", ContainsNumerics::class),
            'with a function call as the value' => \sprintf('SELECT LEAD_OVER(ABS(e.integer1), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with an arithmetic expression as the value' => \sprintf('SELECT LEAD_OVER(e.integer1 + 1, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with parameters' => \sprintf('SELECT LEAD_OVER(:value, :offset, :default, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
        ];
    }

    #[Test]
    public function throws_exception_for_missing_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('lead() requires between 1 and 3 arguments');

        $dql = \sprintf('SELECT LEAD_OVER() FROM %s e', ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_window_specification_without_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('lead() requires between 1 and 3 arguments');

        $dql = \sprintf('SELECT LEAD_OVER(ORDER BY e.integer2) FROM %s e', ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('lead() requires between 1 and 3 arguments');

        $dql = \sprintf('SELECT LEAD_OVER(e.integer1, 1, 0, 5, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_window_specification_without_a_separating_comma(): void
    {
        $this->expectException(QueryException::class);

        $dql = \sprintf('SELECT LEAD_OVER(e.integer1, 1 ORDER BY e.integer2) FROM %s e', ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
