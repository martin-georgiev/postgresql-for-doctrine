<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TransactionTimestamp;
use PHPUnit\Framework\Attributes\Test;

class TransactionTimestampTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TRANSACTION_TIMESTAMP' => TransactionTimestamp::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'returns start of current transaction timestamp' => 'SELECT transaction_timestamp() AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'returns start of current transaction timestamp' => \sprintf('SELECT TRANSACTION_TIMESTAMP() FROM %s e', ContainsTexts::class),
        ];
    }

    #[Test]
    public function throws_exception_when_argument_is_provided(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('transaction_timestamp() requires exactly 0 arguments');

        $dql = \sprintf("SELECT TRANSACTION_TIMESTAMP('now') FROM %s e", ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
