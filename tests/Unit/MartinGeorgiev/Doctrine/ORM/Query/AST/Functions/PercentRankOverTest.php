<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PercentRankOver;
use PHPUnit\Framework\Attributes\Test;

final class PercentRankOverTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'PERCENT_RANK_OVER' => PercentRankOver::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'without a window specification' => 'SELECT percent_rank() OVER () AS sclr_0 FROM ContainsNumerics c0_',
            'with PARTITION BY' => 'SELECT percent_rank() OVER (PARTITION BY c0_.integer1) AS sclr_0 FROM ContainsNumerics c0_',
            'with ORDER BY' => 'SELECT percent_rank() OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with PARTITION BY and ORDER BY' => 'SELECT percent_rank() OVER (PARTITION BY c0_.integer1 ORDER BY c0_.integer2 DESC) AS sclr_0 FROM ContainsNumerics c0_',
            'with multiple PARTITION BY expressions' => 'SELECT percent_rank() OVER (PARTITION BY c0_.integer1, c0_.bigint1 ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'without a window specification' => \sprintf('SELECT PERCENT_RANK_OVER() FROM %s e', ContainsNumerics::class),
            'with PARTITION BY' => \sprintf('SELECT PERCENT_RANK_OVER(PARTITION BY e.integer1) FROM %s e', ContainsNumerics::class),
            'with ORDER BY' => \sprintf('SELECT PERCENT_RANK_OVER(ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with PARTITION BY and ORDER BY' => \sprintf('SELECT PERCENT_RANK_OVER(PARTITION BY e.integer1 ORDER BY e.integer2 DESC) FROM %s e', ContainsNumerics::class),
            'with multiple PARTITION BY expressions' => \sprintf('SELECT PERCENT_RANK_OVER(PARTITION BY e.integer1, e.bigint1 ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
        ];
    }

    #[Test]
    public function throws_exception_when_argument_is_provided(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('percent_rank() requires exactly 0 arguments');

        $dql = \sprintf('SELECT PERCENT_RANK_OVER(e.integer1) FROM %s e', ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
