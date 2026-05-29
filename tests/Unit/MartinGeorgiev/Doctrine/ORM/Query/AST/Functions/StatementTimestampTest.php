<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StatementTimestamp;
use PHPUnit\Framework\Attributes\Test;

class StatementTimestampTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STATEMENT_TIMESTAMP' => StatementTimestamp::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'returns start of current statement timestamp' => 'SELECT statement_timestamp() AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'returns start of current statement timestamp' => \sprintf('SELECT STATEMENT_TIMESTAMP() FROM %s e', ContainsTexts::class),
        ];
    }

    #[Test]
    public function throws_exception_when_argument_is_provided(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('statement_timestamp() requires exactly 0 arguments');

        $dql = \sprintf("SELECT STATEMENT_TIMESTAMP('now') FROM %s e", ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
