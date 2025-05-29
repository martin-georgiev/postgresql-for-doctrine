<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTimestamp;
use PHPUnit\Framework\Attributes\Test;

class ToTimestampTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_TIMESTAMP' => ToTimestamp::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT to_timestamp(c0_.text1, 'DD Mon YYYY') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT TO_TIMESTAMP(e.text1, 'DD Mon YYYY') FROM %s e", ContainsTexts::class),
        ];
    }

    #[Test]
    public function throws_exception_when_argument_missing(): void
    {
        $this->expectException(QueryException::class);

        $dql = \sprintf('SELECT TO_TIMESTAMP(e.text1) FROM %s e', ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
