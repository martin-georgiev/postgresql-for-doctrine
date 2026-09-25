<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Over;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RowNumber;
use PHPUnit\Framework\Attributes\Test;

final class RowNumberTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'OVER' => Over::class,
            'ROW_NUMBER' => RowNumber::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'without a window specification' => 'SELECT row_number() OVER () AS sclr_0 FROM ContainsNumerics c0_',
            'with PARTITION BY' => 'SELECT row_number() OVER (PARTITION BY c0_.integer1) AS sclr_0 FROM ContainsNumerics c0_',
            'with ORDER BY' => 'SELECT row_number() OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with PARTITION BY and ORDER BY' => 'SELECT row_number() OVER (PARTITION BY c0_.integer1 ORDER BY c0_.integer2 DESC) AS sclr_0 FROM ContainsNumerics c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'without a window specification' => \sprintf('SELECT OVER(ROW_NUMBER()) FROM %s e', ContainsNumerics::class),
            'with PARTITION BY' => \sprintf('SELECT OVER(ROW_NUMBER(), PARTITION BY e.integer1) FROM %s e', ContainsNumerics::class),
            'with ORDER BY' => \sprintf('SELECT OVER(ROW_NUMBER(), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with PARTITION BY and ORDER BY' => \sprintf('SELECT OVER(ROW_NUMBER(), PARTITION BY e.integer1 ORDER BY e.integer2 DESC) FROM %s e', ContainsNumerics::class),
        ];
    }

    #[Test]
    public function throws_exception_when_argument_is_provided(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('row_number() requires exactly 0 arguments');

        $dql = \sprintf('SELECT OVER(ROW_NUMBER(1), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
