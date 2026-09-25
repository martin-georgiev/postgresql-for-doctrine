<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use Fixtures\MartinGeorgiev\Doctrine\Function\TestWindowFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use PHPUnit\Framework\Attributes\Test;

final class BaseWindowFunctionTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TEST_WINDOW' => TestWindowFunction::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'with the required argument only' => 'SELECT test_window(c0_.integer1) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with the optional argument' => 'SELECT test_window(c0_.integer1, 2) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'with the required argument only' => \sprintf('SELECT TEST_WINDOW(e.integer1, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with the optional argument' => \sprintf('SELECT TEST_WINDOW(e.integer1, 2, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('test_window() requires between 1 and 2 arguments');

        $dql = \sprintf('SELECT TEST_WINDOW(e.integer1, 2, 3, ORDER BY e.integer2) FROM %s e', ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
