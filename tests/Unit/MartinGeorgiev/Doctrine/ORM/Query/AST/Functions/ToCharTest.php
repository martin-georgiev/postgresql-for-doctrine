<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToChar;
use PHPUnit\Framework\Attributes\Test;

class ToCharTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_CHAR' => ToChar::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT to_char(c0_.datetime1, 'HH12:MI:SS') AS sclr_0 FROM ContainsDates c0_",
            "SELECT to_char(c0_.dateinterval1, 'HH24:MI:SS') AS sclr_0 FROM ContainsDates c0_",
            "SELECT to_char(c0_.decimal1, '999D99S') AS sclr_0 FROM ContainsDecimals c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT TO_CHAR(e.datetime1, 'HH12:MI:SS') FROM %s e", ContainsDates::class),
            \sprintf("SELECT TO_CHAR(e.dateinterval1, 'HH24:MI:SS') FROM %s e", ContainsDates::class),
            \sprintf("SELECT TO_CHAR(e.decimal1, '999D99S') FROM %s e", ContainsDecimals::class),
        ];
    }

    #[Test]
    public function throws_exception_when_argument_is_missing(): void
    {
        $this->expectException(QueryException::class);

        $dql = \sprintf('SELECT TO_CHAR(e.decimal1) FROM %s e', ContainsDecimals::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
