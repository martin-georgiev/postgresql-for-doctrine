<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ClockTimestamp;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use PHPUnit\Framework\Attributes\Test;

class ClockTimestampTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CLOCK_TIMESTAMP' => ClockTimestamp::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'returns current timestamp' => 'SELECT clock_timestamp() AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'returns current timestamp' => \sprintf('SELECT CLOCK_TIMESTAMP() FROM %s e', ContainsTexts::class),
        ];
    }

    #[Test]
    public function throws_exception_when_argument_is_provided(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('clock_timestamp() requires exactly 0 arguments');

        $dql = \sprintf("SELECT CLOCK_TIMESTAMP('now') FROM %s e", ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
